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
    'model_label'        => 'Factura',
    'plural_model_label' => 'Facturas',

    // ----- Navigation -----
    'nav_label'        => 'Facturas',
    'tabs_outer'       => 'Factura',

    // ----- Filter labels -----
    'filter_label_status' => 'Estado',

    // ----- Tabs -----
    'tab_info'         => 'Información',
    'tab_line_items'   => 'Líneas de detalle',
    'tab_totals'       => 'Totales',

    // ----- Info -----
    'lead'             => 'Cliente potencial',
    'company'          => 'Empresa',
    'issued'           => 'Emitida',
    'due_date'         => 'Fecha de vencimiento',

    // ----- Items -----
    'add_item'         => 'Añadir línea',
    'product'          => 'Producto',
    'unit_price'       => 'Precio unitario',
    'discount_percent' => '% de descuento',
    'line_total'       => 'Total de línea',
    'line_total_placeholder' => 'auto',
    'line_item_default_label' => 'Nueva línea',

    // ----- Totals -----
    'tax_rate'         => 'Tasa de impuesto',
    'additional_discount' => 'Descuento adicional',

    // ----- Table -----
    'col_number'       => 'Número',
    'col_lead'         => 'Cliente potencial',
    'col_paid'         => 'Pagado',
    'col_due'          => 'Pendiente',

    // ----- Row actions -----
    'send'             => 'Enviar',
    'download_pdf'     => 'Descargar PDF',
    'record_payment'   => 'Registrar pago',
    'mark_as_paid'     => 'Marcar como pagada',
    'cancel'           => 'Cancelar',
    'refund'           => 'Reembolsar',
    'refund_modal_heading' => 'Marcar factura como reembolsada',
    'refund_modal_description' => 'Esto registra el reembolso en el libro mayor de pagos y establece el estado de la factura como reembolsada. NO reembolsa automáticamente a través de la pasarela.',
    'more'             => 'Más',
    'mark_as_sent'     => 'Marcar como enviada',

    // ----- Record Payment form -----
    'external_reference' => 'Referencia externa',

    // ----- Sub-page actions -----
    'public_link'      => 'Enlace público',

    // ----- Notifications -----
    'notify_no_email'           => 'El cliente potencial no tiene correo electrónico.',
    'notify_sent'               => 'Factura enviada.',
    'notify_send_failed_prefix' => 'Falló: ',
    'notify_payment_recorded'   => 'Pago registrado.',
    'notify_marked_paid'        => 'Factura marcada como pagada.',
    'notify_cancelled'          => 'Factura cancelada.',
    'notify_refunded'           => 'Factura reembolsada.',
    'notify_bulk_marked_sent'   => 'Facturas marcadas como enviadas.',
    'notify_invoice_saved'      => 'Factura guardada.',

    // ----- Payments relation manager -----
    'payments_relation_title'   => 'Pagos',
    'col_amount'                => 'Importe',
    'col_method'                => 'Método',
    'col_received_at'           => 'Recibido el',
    'col_reference'             => 'Referencia',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'       => 'Borrador',
    'option_status_sent'        => 'Enviada',
    'option_status_partial'     => 'Parcial',
    'option_status_paid'        => 'Pagada',
    'option_status_overdue'     => 'Vencida',
    'option_status_cancelled'   => 'Cancelada',
    'option_status_refunded'    => 'Reembolsada',
    'option_gateway_manual'     => 'Manual (Transferencia bancaria)',
    'option_payment_succeeded'  => 'Correcto',
    'option_payment_pending'    => 'Pendiente',
    'option_payment_failed'     => 'Fallido',
    'option_payment_refunded'   => 'Reembolsado',

    // ─── Form field labels ─────────────────────────────────────────
    'field_currency_label'      => 'Moneda',
    'field_status_label'        => 'Estado',
    'field_notes_label'         => 'Notas',
    'field_item_name_label'     => 'Nombre',
    'field_item_description_label' => 'Descripción',
    'field_item_quantity_label' => 'Cantidad',
    'field_subtotal_label'      => 'Subtotal',
    'field_tax_amount_label'    => 'Importe de impuesto',
    'field_total_label'         => 'Total',
    'field_amount_paid_label'   => 'Importe pagado',

    // ─── Record-payment form field labels ──────────────────────────
    'field_gateway_label'       => 'Pasarela',
    'field_amount_label'        => 'Importe',
    'field_payment_status_label' => 'Estado del pago',
    'field_paid_at_label'       => 'Pagado el',

    // ─── Table column labels ───────────────────────────────────────
    'col_total'                 => 'Total',
    'col_status'                => 'Estado',
    'col_created_at'            => 'Creada',

    // ─── Payments relation manager labels ──────────────────────────
    'field_payment_currency_label' => 'Moneda',
    'col_gateway'               => 'Pasarela',
    'col_payment_status'        => 'Estado',
    'col_paid_at'               => 'Pagado el',
    'col_payment_created_at'    => 'Creado',

    // ─── Status badge labels (table column display) ────────────────
    'status_draft'              => 'Borrador',
    'status_sent'               => 'Enviada',
    'status_partial'            => 'Parcial',
    'status_overdue'            => 'Vencida',
    'status_paid'               => 'Pagada',
    'status_cancelled'          => 'Cancelada',
    'status_refunded'           => 'Reembolsada',

    // ─── Payment gateway badge labels (brand names left literal) ───
    'payment_gateway_stripe'    => 'Stripe',
    'payment_gateway_paypal'    => 'PayPal',
    'payment_gateway_razorpay'  => 'Razorpay',
    'payment_gateway_paystack'  => 'Paystack',
    'payment_gateway_manual'    => 'Manual',

    // ─── Payment status badge labels ───────────────────────────────
    'payment_status_pending'    => 'Pendiente',
    'payment_status_succeeded'  => 'Correcto',
    'payment_status_failed'     => 'Fallido',
    'payment_status_refunded'   => 'Reembolsado',
];
