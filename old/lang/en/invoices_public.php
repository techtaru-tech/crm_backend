<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Public invoice page (resources/views/public/invoice/show.blade.php)
|------------------------------------------------------------
| Accessed via __('invoices_public.<key>').
*/

return [

    // ─── Layout / header ───────────────────────────────────────────
    'page_title'           => 'Invoice :number',
    'invoice_label_prefix' => 'Invoice :number',
    'from_quote_prefix'    => 'From Quote :number',
    'amount_due'           => 'Amount due',

    // ─── Meta boxes ────────────────────────────────────────────────
    'issued'               => 'Issued',
    'due'                  => 'Due',
    'bill_to'              => 'Bill to',

    // Section headings
    'items_heading'        => 'Items',

    // ─── Items table ───────────────────────────────────────────────
    'col_item'             => 'Item',
    'col_qty'              => 'Qty',
    'col_unit'             => 'Unit',
    'col_total'            => 'Total',
    'subtotal'             => 'Subtotal',
    'discount'             => 'Discount',
    'tax_label'            => 'Tax (:rate%)',
    'total'                => 'Total',
    'paid'                 => 'Paid',

    // ─── Paid badge / footer ───────────────────────────────────────
    'paid_in_full'         => 'Paid in full',
    'marked_paid_on'       => 'Marked paid on :date',
    'download_pdf'         => 'Download PDF',
    'secured_link_suffix'  => 'Secured link — :app',

    // ─── Paid page (resources/views/public/invoice/paid.blade.php) ──
    'paid_title_suffix'    => 'Invoice :number — Paid',
    'paid_heading_paid'    => 'Payment received',
    'paid_heading_thanks'  => 'Thank you',
    'paid_invoice_label'   => 'Invoice :number',
    'paid_total_label'     => 'Total',
    'paid_received_body'   => 'Your payment has been received. A receipt will be emailed to you.',
    'paid_pending_body'    => 'Your payment is being processed. This page will update once confirmation is received.',
    'paid_download_pdf'    => 'Download PDF',

    // ─── Status pill labels (Pass 22) ────────────────────────────
    // Used by resources/views/public/invoice/show.blade.php to display
    // the invoice status to recipients in their locale. Mirrors the
    // admin-side `filament/invoices.status_*` keys for parity.
    'status_draft'         => 'Draft',
    'status_sent'          => 'Sent',
    'status_partial'       => 'Partial',
    'status_overdue'       => 'Overdue',
    'status_paid'          => 'Paid',
    'status_cancelled'     => 'Cancelled',
    'status_refunded'      => 'Refunded',
];
