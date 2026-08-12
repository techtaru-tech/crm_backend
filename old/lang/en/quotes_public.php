<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Public quote page (resources/views/public/quote/show.blade.php)
|------------------------------------------------------------
| Accessed via __('quotes_public.<key>').
*/

return [

    // ─── Layout ────────────────────────────────────────────────────
    'quote_label_prefix'       => 'Quote :number',
    'valid_until_prefix'       => 'Valid until :date',
    'grand_total'              => 'Grand total',

    // ─── Status pills ──────────────────────────────────────────────
    'status_accepted'          => 'Accepted',
    'status_declined'          => 'Declined',
    'status_expired'           => 'Expired',
    'status_awaiting_review'   => 'Awaiting your review',
    // Additional keys used by the public PDF (resources/views/public/quote/pdf.blade.php)
    // which renders the raw status enum as a pill. Match Quote::STATUS_* constants.
    'status_draft'             => 'Draft',
    'status_sent'              => 'Sent',
    'status_converted'         => 'Converted',

    // ─── Items table ───────────────────────────────────────────────
    'items_heading'            => 'Items',
    'col_item'                 => 'Item',
    'col_qty'                  => 'Qty',
    'col_unit'                 => 'Unit',
    'col_total'                => 'Total',
    'subtotal'                 => 'Subtotal',
    'discount'                 => 'Discount',
    'tax_label'                => 'Tax (:rate%)',
    'total'                    => 'Total',
    'terms_conditions'         => 'Terms & Conditions',
    'page_title'               => 'Quote :number',

    // ─── Response form ─────────────────────────────────────────────
    'your_response'            => 'Your response',
    'type_full_name_to_sign'   => 'Type your full name to sign',
    'name_placeholder'         => 'e.g. Jane Doe',
    'agree_legal_text'         => 'I agree to the terms above. By clicking Accept I provide a legally binding typed signature. My IP address (:ip) and the date/time will be recorded for audit.',
    'accept_and_sign'          => 'Accept & Sign',
    'decline_with_reason'      => 'Decline with a reason',
    'decline_placeholder'      => 'Let us know why (optional but helpful)…',
    'decline'                  => 'Decline',
    'download_pdf'             => 'Download PDF',
    'no_longer_accepting'      => 'This quote is no longer accepting responses.',
    'secured_link_suffix'      => 'Secured link — :app',

    // ─── Accepted page (resources/views/public/quote/accepted.blade.php) ──
    'accepted_title_suffix'    => 'Quote :number — Accepted',
    'accepted_heading'         => 'Thank you!',
    'accepted_quote_label'     => 'Quote :number',
    'accepted_recorded_body'   => 'Your acceptance has been recorded with a timestamped, IP-logged signature.',
    'accepted_next_step'       => 'Next step',
    'accepted_invoice_body'    => 'Invoice :number has been created for this order. You can pay it here:',
    'accepted_view_pay_invoice'=> 'View & Pay Invoice',
    'accepted_what_next'       => 'What happens next',
    'accepted_invoice_pending' => 'Your invoice will be sent to you shortly.',
];
