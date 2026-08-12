<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Customer-facing quote endpoints — no auth. The only gate is the
 * opaque 64-char `public_token` on the Quote row, plus the throttle
 * middleware declared in routes/web.php.
 */
class PublicQuoteController extends Controller
{
    public function show(string $token): View|Response
    {
        $quote = $this->resolve($token);

        return response()->view('public.quote.show', [
            'quote' => $quote,
        ])->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function accept(string $token, Request $request)
    {
        // Validate first — happens outside the transaction so a malformed
        // form submission doesn't lock the row uselessly.  Laravel's
        // ValidationException auto-redirects back to the form.
        $data = $request->validate([
            'signed_name' => 'required|string|max:150',
            'agreed'      => 'required|accepted',
        ]);

        // M-S1: lockForUpdate inside a transaction makes the
        // status-check + markAccepted + convertToInvoice atomic.  Two
        // concurrent clicks on the Accept button (impatient customer +
        // browser tab restore on flaky mobile data) used to both pass
        // the status check, both call markAccepted, and both call
        // convertToInvoice — yielding two invoice rows for one quote.
        // With the row lock the second request waits, sees the
        // already-ACCEPTED status, and redirects to the existing
        // accepted view instead.
        return DB::transaction(function () use ($token, $request, $data) {
            $quote = Quote::withoutGlobalScope('tenant')
                ->with(['items.product', 'lead', 'company', 'tenant'])
                ->where('public_token', $token)
                ->lockForUpdate()
                ->first();

            abort_if(! $quote, 404);

            if ($quote->status === Quote::STATUS_ACCEPTED || $quote->status === Quote::STATUS_CONVERTED) {
                return redirect()->route('quote.show', $token)
                    ->with('info', __('messages.quote_already_accepted'));
            }

            $quote->markAccepted($data['signed_name'], (string) $request->ip());

            // Auto-convert to invoice so the tenant can send it right
            // away.  Inside the transaction so a convertToInvoice
            // failure rolls back the quote-status change too — the
            // customer can retry without ending up in a half-accepted
            // state.
            try {
                $invoice = $quote->convertToInvoice();
            } catch (\Throwable $e) {
                Log::error('[quote.accept] convertToInvoice failed', ['error' => $e->getMessage()]);
                $invoice = null;
            }

            $this->notifyTenant(
                $quote,
                __('quotes_mail.accepted_subject'),
                $invoice
                    ? __('quotes_mail.accepted_body_with_invoice', [
                        'number'  => $quote->quote_number,
                        'signer'  => $data['signed_name'],
                        'invoice' => $invoice->invoice_number,
                    ])
                    : __('quotes_mail.accepted_body_no_invoice', [
                        'number' => $quote->quote_number,
                        'signer' => $data['signed_name'],
                    ])
            );

            return view('public.quote.accepted', [
                'quote'   => $quote->fresh(),
                'invoice' => $invoice,
            ]);
        });
    }

    public function decline(string $token, Request $request)
    {
        $quote = $this->resolve($token);

        if ($quote->status === Quote::STATUS_ACCEPTED || $quote->status === Quote::STATUS_CONVERTED) {
            return redirect()->route('quote.show', $token)->with('info', __('messages.quote_already_accepted_cannot_decline'));
        }

        $data = $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);

        $quote->markDeclined($data['decline_reason']);

        $this->notifyTenant(
            $quote,
            __('quotes_mail.declined_subject'),
            __('quotes_mail.declined_body', [
                'number' => $quote->quote_number,
                'reason' => $data['decline_reason'],
            ])
        );

        return redirect()->route('quote.show', $token)->with('info', __('messages.quote_response_recorded'));
    }

    public function pdf(string $token): SymfonyResponse
    {
        $quote = $this->resolve($token);

        $pdf = Pdf::loadView('public.quote.pdf', [
            'quote' => $quote,
            'logo'  => \App\Support\PdfBranding::logoDataUri($quote->tenant),
        ])->setPaper('a4');

        return $pdf->stream($quote->quote_number . '.pdf');
    }

    /* -------------------------------------------------------- */

    protected function resolve(string $token): Quote
    {
        $quote = Quote::withoutGlobalScope('tenant')
            ->with(['items.product', 'lead', 'company', 'tenant'])
            ->where('public_token', $token)
            ->first();

        abort_if(! $quote, 404);

        return $quote;
    }

    /**
     * Best-effort email notification to the quote creator. Silently
     * swallows mail config errors so the customer-facing flow never
     * 500s on a mis-configured SMTP server.
     */
    protected function notifyTenant(Quote $quote, string $subject, string $body): void
    {
        $to = $quote->creator?->email ?? $quote->tenant?->owner?->email ?? null;
        if (! $to) {
            return;
        }

        try {
            Mail::raw($body, function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('[quote.notify] ' . $e->getMessage());
        }
    }
}
