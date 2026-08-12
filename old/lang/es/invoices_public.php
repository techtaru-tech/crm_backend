<?php

declare(strict_types=1);

return [

    // ─── Layout / encabezado ───────────────────────────────────────
    'page_title'           => 'Factura :number',
    'invoice_label_prefix' => 'Factura :number',
    'from_quote_prefix'    => 'Desde Presupuesto :number',
    'amount_due'           => 'Importe pendiente',

    // ─── Cajas de metadatos ────────────────────────────────────────
    'issued'               => 'Emitida',
    'due'                  => 'Vence',
    'bill_to'              => 'Facturar a',

    // Encabezados de sección
    'items_heading'        => 'Conceptos',

    // ─── Tabla de conceptos ────────────────────────────────────────
    'col_item'             => 'Concepto',
    'col_qty'              => 'Cant.',
    'col_unit'             => 'Unitario',
    'col_total'            => 'Total',
    'subtotal'             => 'Subtotal',
    'discount'             => 'Descuento',
    'tax_label'            => 'Impuesto (:rate%)',
    'total'                => 'Total',
    'paid'                 => 'Pagada',

    // ─── Insignia de pagada / pie ──────────────────────────────────
    'paid_in_full'         => 'Pagada en su totalidad',
    'marked_paid_on'       => 'Marcada como pagada el :date',
    'download_pdf'         => 'Descargar PDF',
    'secured_link_suffix'  => 'Enlace seguro — :app',

    // ─── Página de pagada ──────────────────────────────────────────
    'paid_title_suffix'    => 'Factura :number — Pagada',
    'paid_heading_paid'    => 'Pago recibido',
    'paid_heading_thanks'  => 'Gracias',
    'paid_invoice_label'   => 'Factura :number',
    'paid_total_label'     => 'Total',
    'paid_received_body'   => 'Hemos recibido su pago. Le enviaremos un recibo por correo electrónico.',
    'paid_pending_body'    => 'Su pago se está procesando. Esta página se actualizará cuando se reciba la confirmación.',
    'paid_download_pdf'    => 'Descargar PDF',

    // ─── Etiquetas de estado ───────────────────────────────────────
    'status_draft'         => 'Borrador',
    'status_sent'          => 'Enviada',
    'status_partial'       => 'Parcial',
    'status_overdue'       => 'Vencida',
    'status_paid'          => 'Pagada',
    'status_cancelled'     => 'Cancelada',
    'status_refunded'      => 'Reembolsada',
];
