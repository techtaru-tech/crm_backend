<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| InvoiceResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/invoices.<key>').
*/

return [

    // ----- Model labels -----
    'model_label'        => 'Invoice',
    'plural_model_label' => 'Invoices',

    // ----- Navigation -----
    'nav_label'        => 'Invoices',
    'tabs_outer'       => 'Invoice',

    // ----- Filter labels -----
    'filter_label_status' => 'Status',

    // ----- Tabs -----
    'tab_info'         => 'Info',
    'tab_line_items'   => 'Line Items',
    'tab_totals'       => 'Totals',

    // ----- Info -----
    'lead'             => 'Lead',
    'company'          => 'Company',
    'issued'           => 'Issued',
    'due_date'         => 'Due Date',

    // ----- Items -----
    'add_item'         => 'Add Item',
    'product'          => 'Product',
    'unit_price'       => 'Unit Price',
    'discount_percent' => 'Discount %',
    'line_total'       => 'Line Total',
    'line_total_placeholder' => 'auto',
    'line_item_default_label' => 'New item',

    // ----- Totals -----
    'tax_rate'         => 'Tax Rate',
    'additional_discount' => 'Additional Discount',

    // ----- Table -----
    'col_number'       => 'Number',
    'col_lead'         => 'Lead',
    'col_paid'         => 'Paid',
    'col_due'          => 'Due',

    // ----- Row actions -----
    'send'             => 'Send',
    'download_pdf'     => 'Download PDF',
    'record_payment'   => 'Record Payment',
    'mark_as_paid'     => 'Mark as Paid',
    'cancel'           => 'Cancel',
    'refund'           => 'Refund',
    'refund_modal_heading' => 'Mark invoice as refunded',
    'refund_modal_description' => 'This records the refund in the payments ledger and sets the invoice status to refunded. It does NOT automatically refund through the gateway.',
    'more'             => 'More',
    'mark_as_sent'     => 'Mark as Sent',

    // ----- Record Payment form -----
    'external_reference' => 'External reference',

    // ----- Sub-page actions -----
    'public_link'      => 'Public Link',

    // ----- Notifications -----
    'notify_no_email'           => 'Lead has no email.',
    'notify_sent'               => 'Invoice sent.',
    'notify_send_failed_prefix' => 'Failed: ',
    'notify_payment_recorded'   => 'Payment recorded.',
    'notify_marked_paid'        => 'Invoice marked as paid.',
    'notify_cancelled'          => 'Invoice cancelled.',
    'notify_refunded'           => 'Invoice refunded.',
    'notify_bulk_marked_sent'   => 'Invoices marked as sent.',
    'notify_invoice_saved'      => 'Invoice saved.',

    // ----- Payments relation manager -----
    'payments_relation_title'   => 'Payments',
    'col_amount'                => 'Amount',
    'col_method'                => 'Method',
    'col_received_at'           => 'Received At',
    'col_reference'             => 'Reference',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'       => 'Draft',
    'option_status_sent'        => 'Sent',
    'option_status_partial'     => 'Partial',
    'option_status_paid'        => 'Paid',
    'option_status_overdue'     => 'Overdue',
    'option_status_cancelled'   => 'Cancelled',
    'option_status_refunded'    => 'Refunded',
    'option_gateway_manual'     => 'Manual (Bank transfer)',
    'option_payment_succeeded'  => 'Succeeded',
    'option_payment_pending'    => 'Pending',
    'option_payment_failed'     => 'Failed',
    'option_payment_refunded'   => 'Refunded',

    // ─── Form field labels ─────────────────────────────────────────
    'field_currency_label'      => 'Currency',
    'field_status_label'        => 'Status',
    'field_notes_label'         => 'Notes',
    'field_item_name_label'     => 'Name',
    'field_item_description_label' => 'Description',
    'field_item_quantity_label' => 'Quantity',
    'field_subtotal_label'      => 'Subtotal',
    'field_tax_amount_label'    => 'Tax Amount',
    'field_total_label'         => 'Total',
    'field_amount_paid_label'   => 'Amount Paid',

    // ─── Record-payment form field labels ──────────────────────────
    'field_gateway_label'       => 'Gateway',
    'field_amount_label'        => 'Amount',
    'field_payment_status_label' => 'Payment Status',
    'field_paid_at_label'       => 'Paid At',

    // ─── Table column labels ───────────────────────────────────────
    'col_total'                 => 'Total',
    'col_status'                => 'Status',
    'col_created_at'            => 'Created',

    // ─── Payments relation manager labels ──────────────────────────
    'field_payment_currency_label' => 'Currency',
    'col_gateway'               => 'Gateway',
    'col_payment_status'        => 'Status',
    'col_paid_at'               => 'Paid At',
    'col_payment_created_at'    => 'Created',

    // ─── Status badge labels (table column display) ────────────────
    'status_draft'              => 'Draft',
    'status_sent'               => 'Sent',
    'status_partial'            => 'Partial',
    'status_overdue'            => 'Overdue',
    'status_paid'               => 'Paid',
    'status_cancelled'          => 'Cancelled',
    'status_refunded'           => 'Refunded',

    // ─── Payment gateway badge labels (brand names left literal) ───
    'payment_gateway_stripe'    => 'Stripe',
    'payment_gateway_paypal'    => 'PayPal',
    'payment_gateway_razorpay'  => 'Razorpay',
    'payment_gateway_paystack'  => 'Paystack',
    'payment_gateway_manual'    => 'Manual',

    // ─── Payment status badge labels ───────────────────────────────
    'payment_status_pending'    => 'Pending',
    'payment_status_succeeded'  => 'Succeeded',
    'payment_status_failed'     => 'Failed',
    'payment_status_refunded'   => 'Refunded',
];
