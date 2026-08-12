@extends('marketing.layout')

@section('title', __('billing.manual_page_title'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/views/billing/manual-instructions.css') }}">
@endpush

@section('content')
<div class="wrap-narrow">
    <div class="card">
        <h1>{{ __('billing.transfer_instructions') }}</h1>
        <p class="sub">{{ $result->message }}</p>

        <div class="amount">
            <p class="label">{{ __('billing.manual_amount_due_label') }}</p>
            <p class="val">{{ $result->data['amount'] }}</p>
            <p class="sub plan">{{ __('billing_gateways.manual.plan_suffix', ['plan' => $result->data['plan']]) }}</p>
        </div>

        <div class="rows">
            @foreach($result->data['instructions'] as $label => $value)
                <div class="row">
                    <span class="k">{{ $label }}</span>
                    <span class="v">{{ $value }}</span>
                </div>
            @endforeach
        </div>

        @if(! empty($result->data['notes']))
            <div class="notes">{!! nl2br(e($result->data['notes'])) !!}</div>
        @endif

        <a href="{{ url('/admin') }}" class="cta">{{ __('billing.manual_back_to_workspace') }}</a>
    </div>
</div>
@endsection
