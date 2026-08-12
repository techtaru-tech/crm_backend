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
    'model_label'        => 'Presupuesto',
    'plural_model_label' => 'Presupuestos',

    // ----- Navigation -----
    'nav_label'        => 'Presupuestos',
    'tabs_outer'       => 'Presupuesto',

    // ----- Filter labels -----
    'filter_label_status' => 'Estado',

    // ----- Tabs -----
    'tab_info'         => 'Información',
    'tab_line_items'   => 'Líneas de detalle',
    'tab_totals'       => 'Totales',

    // ----- Info -----
    'title'            => 'Título',
    // Default placeholder pre-filled in the "Title" field when an
    // operator clicks "Create Quote" from a lead view (CreateQuote
    // page sets this via __()).
    'new_quote_default_title' => 'Nuevo presupuesto',
    'lead'             => 'Cliente potencial',
    'company'          => 'Empresa',
    'currency'         => 'Moneda',
    'valid_until'      => 'Válido hasta',
    'introduction'     => 'Introducción',
    'terms'            => 'Términos y condiciones',

    // ----- Items -----
    'items_description' => 'Productos o servicios incluidos en este presupuesto.',
    'add_item'         => 'Añadir línea',
    'product'          => 'Producto',
    'name'             => 'Nombre',
    'unit_price'       => 'Precio unitario',
    'discount_percent' => '% de descuento',
    'line_total'       => 'Total de línea',
    'line_total_placeholder' => 'auto',
    'line_item_default_label' => 'Nueva línea',

    // ----- Totals -----
    'subtotal'         => 'Subtotal',
    'tax_rate'         => 'Tasa de impuesto',
    'tax_amount'       => 'Importe de impuesto',
    'additional_discount' => 'Descuento adicional',
    'total'            => 'Total',

    // ----- Table -----
    'col_number'       => 'Número',
    'col_lead'         => 'Cliente potencial',
    'col_valid_until'  => 'Válido hasta',
    'col_created'      => 'Creado',

    // ----- Row actions -----
    'duplicate'        => 'Duplicar',
    'send'             => 'Enviar',
    'send_modal_heading' => 'Enviar presupuesto por correo al cliente potencial',
    'send_modal_description' => 'Envía un enlace a :recipient para ver, firmar y aceptar este presupuesto.',
    'send_modal_recipient_fallback' => 'el cliente potencial',
    'download_pdf'     => 'Descargar PDF',
    'convert_to_invoice' => 'Convertir en factura',
    'more'             => 'Más',

    // ----- Sub-page actions -----
    'send_for_signature' => 'Enviar para firma',
    'preview'          => 'Vista previa',
    'public_link'      => 'Enlace público',
    'cancel'           => 'Cancelar',

    // ----- Notifications -----
    'notif_duplicated'  => 'Presupuesto duplicado.',
    'notif_lead_no_email' => 'El cliente potencial no tiene correo electrónico.',
    'notif_sent'        => 'Presupuesto enviado.',
    'notif_send_failed' => 'Error al enviar: :error',
    'notif_invoice_created' => 'Factura :number creada.',
    'notif_signature_sent' => 'Enlace de firma enviado.',
    'notif_signature_failed' => 'Error: :error',
    'notif_cancelled'   => 'Presupuesto cancelado.',
    'notif_saved'       => 'Presupuesto guardado.',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'     => 'Borrador',
    'option_status_sent'      => 'Enviado',
    'option_status_accepted'  => 'Aceptado',
    'option_status_declined'  => 'Rechazado',
    'option_status_expired'   => 'Caducado',
    'option_status_converted' => 'Convertido',

    // ─── Form field labels ─────────────────────────────────────────
    'field_item_description_label' => 'Descripción',
    'field_item_quantity_label'    => 'Cantidad',

    // ─── Table column labels ───────────────────────────────────────
    'col_title'        => 'Título',
    'col_total'        => 'Total',
    'col_status'       => 'Estado',

    // ─── Status badge labels (table column display) ────────────────
    'status_draft'     => 'Borrador',
    'status_sent'      => 'Enviado',
    'status_accepted'  => 'Aceptado',
    'status_declined'  => 'Rechazado',
    'status_expired'   => 'Caducado',
    'status_converted' => 'Convertido',

    // ─── Duplicate action: suffix appended to the duplicated title ─
    'duplicate_suffix' => '(copia)',

    // ─── Decline reasons (written to quotes.decline_reason column) ─
    'decline_reason_cancelled_by_sender' => 'Cancelado por el remitente',

];
