<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ __('quotes_public.page_title', ['number' => $quote->quote_number]) }}</title>
    <link rel="stylesheet" href="{{ asset('css/views/public/quote/show.css') }}">
</head>
<body>
<div class="wrap">
    @if(session('info'))
        <div class="notice">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="hdr">
            <div class="meta">
                <div>
                    <h1>{{ $quote->title }}</h1>
                    <div class="num">{{ __('quotes_public.quote_label_prefix', ['number' => $quote->quote_number]) }}</div>
                    @if($quote->valid_until)
                        <div class="valid qs-valid-soft">{{ __('quotes_public.valid_until_prefix', ['date' => $quote->valid_until->translatedFormat('M j, Y')]) }}</div>
                    @endif
                </div>
                <div class="total">
                    {{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}
                    <small>{{ __('quotes_public.grand_total') }}</small>
                </div>
            </div>
        </div>
        <div class="body">
            @if($quote->status === \App\Models\Quote::STATUS_ACCEPTED || $quote->status === \App\Models\Quote::STATUS_CONVERTED)
                <span class="pill pill-success">{{ __('quotes_public.status_accepted') }}</span>
            @elseif($quote->status === \App\Models\Quote::STATUS_DECLINED)
                <span class="pill pill-danger">{{ __('quotes_public.status_declined') }}</span>
            @elseif($quote->status === \App\Models\Quote::STATUS_EXPIRED)
                <span class="pill pill-danger">{{ __('quotes_public.status_expired') }}</span>
            @else
                <span class="pill pill-info">{{ __('quotes_public.status_awaiting_review') }}</span>
            @endif

            @if($quote->introduction)
                <p class="intro qs-intro">{{ $quote->introduction }}</p>
            @endif

            <h2 class="qs-items-head">{{ __('quotes_public.items_heading') }}</h2>
            <table>
                <thead>
                <tr>
                    <th>{{ __('quotes_public.col_item') }}</th>
                    <th class="r">{{ __('quotes_public.col_qty') }}</th>
                    <th class="r">{{ __('quotes_public.col_unit') }}</th>
                    <th class="r">{{ __('quotes_public.col_total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($quote->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)
                                <div class="desc">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="r">{{ $item->quantity }}</td>
                        <td class="r">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="r"><strong>{{ number_format((float) $item->total, 2) }}</strong></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="tot">
                <div class="tot-row"><span>{{ __('quotes_public.subtotal') }}</span><span>{{ number_format((float) $quote->subtotal, 2) }} {{ $quote->currency }}</span></div>
                @if((float) $quote->discount_amount > 0)
                    <div class="tot-row"><span>{{ __('quotes_public.discount') }}</span><span>− {{ number_format((float) $quote->discount_amount, 2) }} {{ $quote->currency }}</span></div>
                @endif
                @if((float) $quote->tax_rate > 0)
                    <div class="tot-row"><span>{{ __('quotes_public.tax_label', ['rate' => number_format((float) $quote->tax_rate, 2)]) }}</span><span>{{ number_format((float) $quote->tax_amount, 2) }} {{ $quote->currency }}</span></div>
                @endif
                <div class="tot-row grand"><span>{{ __('quotes_public.total') }}</span><span>{{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}</span></div>
            </div>

            @if($quote->terms)
                <div class="terms">
                    <strong class="qs-terms-strong">{{ __('quotes_public.terms_conditions') }}</strong>
                    {{ $quote->terms }}
                </div>
            @endif
        </div>
    </div>

    @if(in_array($quote->status, [\App\Models\Quote::STATUS_DRAFT, \App\Models\Quote::STATUS_SENT], true))
        <div class="card">
            <div class="body">
                <h2>{{ __('quotes_public.your_response') }}</h2>
                <div class="actions">
                    <form method="POST" action="{{ route('quote.accept', $quote->public_token) }}" class="qs-form-flush">
                        @csrf
                        <label for="signed_name">{{ __('quotes_public.type_full_name_to_sign') }}</label>
                        <input type="text" id="signed_name" name="signed_name" required maxlength="150" placeholder="{{ __('quotes_public.name_placeholder') }}">
                        <div class="agree">
                            <input type="checkbox" id="agreed" name="agreed" value="1" required>
                            <label for="agreed" class="qs-agree-label">{{ __('quotes_public.agree_legal_text', ['ip' => request()->ip()]) }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary qs-w-full">{{ __('quotes_public.accept_and_sign') }}</button>
                    </form>

                    <form method="POST" action="{{ route('quote.decline', $quote->public_token) }}" class="qs-form-flush">
                        @csrf
                        <label for="decline_reason">{{ __('quotes_public.decline_with_reason') }}</label>
                        <textarea id="decline_reason" name="decline_reason" rows="3" maxlength="500" placeholder="{{ __('quotes_public.decline_placeholder') }}" required></textarea>
                        <button type="submit" class="btn btn-danger qs-w-full qs-mt-8">{{ __('quotes_public.decline') }}</button>
                    </form>
                </div>
                <div class="qs-pdf-row">
                    <a href="{{ route('quote.pdf', $quote->public_token) }}" class="btn btn-gray">{{ __('quotes_public.download_pdf') }}</a>
                </div>
            </div>
        </div>
    @else
        <div class="dead">
            {{ __('quotes_public.no_longer_accepting') }}
            <div class="qs-dead-actions"><a href="{{ route('quote.pdf', $quote->public_token) }}" class="btn btn-gray">{{ __('quotes_public.download_pdf') }}</a></div>
        </div>
    @endif

    <div class="foot">
        {{ __('quotes_public.secured_link_suffix', ['app' => config('app.name')]) }}
    </div>
</div>
</body>
</html>
