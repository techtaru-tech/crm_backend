<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Public legal-document endpoints.
 *
 * GDPR Article 28 requires controllers and processors to enter into a
 * written Data Processing Agreement (DPA).  Without a downloadable
 * template, every B2B prospect's procurement team blocks the deal at
 * the contract stage — a silent conversion killer.  This endpoint
 * gives them a copy they can sign and route through their legal team.
 *
 * The default template ships generic fill-the-blank language sized
 * for a self-hosted CRM.  Buyers branding the install can override
 * resources/views/legal/dpa-template.blade.php in their fork or
 * customize the company name via Filament's branding settings.
 */
class LegalDocumentsController extends Controller
{
    /**
     * GET /legal/dpa.pdf — stream the DPA template as PDF.
     *
     * No auth required — the DPA is a marketing artefact, not a
     * tenant document.  Each tenant who actually onboards a B2B
     * customer will have their own counter-signed copy stored
     * elsewhere.
     */
    public function dpa(): SymfonyResponse
    {
        $companyName = $this->companyName();

        $pdf = Pdf::loadView('legal.dpa-template', [
            'company_name' => $companyName,
            'rendered_at'  => now(),
        ])->setPaper('a4');

        // Translator-routed so buyers can localise the browser
        // save-as filename without forking the controller.
        $filename = (string) __('controllers/legal_documents.pdf_filename') . '.pdf';

        return $pdf->stream($filename);
    }

    protected function companyName(): string
    {
        try {
            $settings = app(\App\Settings\GeneralSettings::class);
            $name = trim((string) ($settings->company_name ?? ''));
            if ($name !== '') return $name;
        } catch (\Throwable) {
            // settings may be unavailable on a fresh install
        }
        return (string) config('app.name', __('controllers/legal_documents.operator_fallback'));
    }
}
