<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| QuoteResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/quotes.<key>').
*/

return [

    // ----- Model labels -----
    'model_label'        => 'Quote',
    'plural_model_label' => 'Quotes',

    // ----- Navigation -----
    'nav_label'        => 'Quotes',
    'tabs_outer'       => 'Quote',

    // ----- Filter labels -----
    'filter_label_status' => 'Status',

    // ----- Tabs -----
    'tab_info'         => 'Info',
    'tab_line_items'   => 'Line Items',
    'tab_totals'       => 'Totals',

    // ----- Info -----
    'title'            => 'Title',
    // Default placeholder pre-filled in the "Title" field when an
    // operator clicks "Create Quote" from a lead view (CreateQuote
    // page sets this via __()).
    'new_quote_default_title' => 'New Quote',
    'lead'             => 'Lead',
    'company'          => 'Company',
    'currency'         => 'Currency',
    'valid_until'      => 'Valid Until',
    'introduction'     => 'Introduction',
    'terms'            => 'Terms & Conditions',

    // ----- Items -----
    'items_description' => 'Products or services included in this quote.',
    'add_item'         => 'Add Item',
    'product'          => 'Product',
    'name'             => 'Name',
    'unit_price'       => 'Unit Price',
    'discount_percent' => 'Discount %',
    'line_total'       => 'Line Total',
    'line_total_placeholder' => 'auto',
    'line_item_default_label' => 'New item',

    // ----- Totals -----
    'subtotal'         => 'Subtotal',
    'tax_rate'         => 'Tax Rate',
    'tax_amount'       => 'Tax Amount',
    'additional_discount' => 'Additional Discount',
    'total'            => 'Total',

    // ----- Table -----
    'col_number'       => 'Number',
    'col_lead'         => 'Lead',
    'col_valid_until'  => 'Valid Until',
    'col_created'      => 'Created',

    // ----- Row actions -----
    'duplicate'        => 'Duplicate',
    'send'             => 'Send',
    'send_modal_heading' => 'Email quote to lead',
    'send_modal_description' => 'Sends a link to :recipient to view, sign, and accept this quote.',
    'send_modal_recipient_fallback' => 'the lead',
    'download_pdf'     => 'Download PDF',
    'convert_to_invoice' => 'Convert to Invoice',
    'more'             => 'More',

    // ----- Sub-page actions -----
    'send_for_signature' => 'Send for Signature',
    'preview'          => 'Preview',
    'public_link'      => 'Public Link',
    'cancel'           => 'Cancel',

    // ----- Notifications -----
    'notif_duplicated'  => 'Quote duplicated.',
    'notif_lead_no_email' => 'Lead has no email.',
    'notif_sent'        => 'Quote sent.',
    'notif_send_failed' => 'Failed to send: :error',
    'notif_invoice_created' => 'Invoice :number created.',
    'notif_signature_sent' => 'Signature link sent.',
    'notif_signature_failed' => 'Failed: :error',
    'notif_cancelled'   => 'Quote cancelled.',
    'notif_saved'       => 'Quote saved.',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'     => 'Draft',
    'option_status_sent'      => 'Sent',
    'option_status_accepted'  => 'Accepted',
    'option_status_declined'  => 'Declined',
    'option_status_expired'   => 'Expired',
    'option_status_converted' => 'Converted',

    // ─── Form field labels ─────────────────────────────────────────
    'field_item_description_label' => 'Description',
    'field_item_quantity_label'    => 'Quantity',

    // ─── Table column labels ───────────────────────────────────────
    'col_title'        => 'Title',
    'col_total'        => 'Total',
    'col_status'       => 'Status',

    // ─── Status badge labels (table column display) ────────────────
    'status_draft'     => 'Draft',
    'status_sent'      => 'Sent',
    'status_accepted'  => 'Accepted',
    'status_declined'  => 'Declined',
    'status_expired'   => 'Expired',
    'status_converted' => 'Converted',

    // ─── Duplicate action: suffix appended to the duplicated title ─
    'duplicate_suffix' => '(copy)',

    // ─── Decline reasons (written to quotes.decline_reason column) ─
    'decline_reason_cancelled_by_sender' => 'Cancelled by sender',

];
