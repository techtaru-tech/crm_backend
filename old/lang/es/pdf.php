<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PDF document strings
|--------------------------------------------------------------------------
|
| Strings rendered into PDF documents (receipts, invoices, quotes,
| analytics exports) via DomPDF.  DomPDF cannot fetch external CSS
| or stylesheets, so PDF templates inline their CSS, but human-
| readable copy still comes through these translation keys so
| buyers can adapt or translate without touching the Blade views.
|
| Placeholders use Laravel's :placeholder convention; pass values via
| __('pdf.key', ['placeholder' => $value]).
|
*/

return [

    // ─── Billing receipt (receipt-pdf.blade.php) ─────────────────────
    'receipt' => [
        'doc_title'          => 'Recibo :number',
        'title'              => 'Recibo',
        'issued'             => 'Emitido :date',
        'from'               => 'De',
        'billed_to'          => 'Facturado a',
        'anonymized'         => '<anonymized>',
        'gdpr_anonymized'    => 'Espacio de trabajo eliminado conforme al artículo 17 del GDPR. Registro fiscal conservado.',
        'vat_label'          => 'IVA/GST: :number',
        'th_description'     => 'Descripción',
        'th_plan'            => 'Plan',
        'th_amount'          => 'Importe',
        'subscription_via'   => 'Pago de suscripción mediante :gateway',
        'ref'                => 'Ref: :ref',
        'total_paid'         => 'Total pagado',
        'payment_method'     => 'Método de pago',
        'auto_footer'        => 'Este recibo se generó automáticamente. Para consultas fiscales, contacte con el soporte de :company.',

        // ─── Gateway names (Pass 22) ─────────────────────────────
        // Stripe/PayPal/Razorpay/Paystack are brand names and ship
        // verbatim — they don't translate.  'manual' however is plain
        // English and needs a localised label so non-English tenants
        // don't see "Manual" mid-receipt.
        'gateway_stripe'     => 'Stripe',
        'gateway_paypal'     => 'PayPal',
        'gateway_razorpay'   => 'Razorpay',
        'gateway_paystack'   => 'Paystack',
        'gateway_manual'     => 'Manual',
    ],

    // ─── Customer invoice (public/invoice/pdf.blade.php) ─────────────
    'invoice' => [
        'doc_title'     => 'Factura :number',
        'label'         => 'FACTURA',
        'issued'        => 'Emitida :date',
        'due'           => 'Vencimiento :date',
        'paid_stamp'    => 'Pagada',
        'from'          => 'De',
        'bill_to'       => 'Facturar a',
        'th_item'       => 'Concepto',
        'th_qty'        => 'Cant.',
        'th_unit'       => 'Unidad (:currency)',
        'th_total'      => 'Total (:currency)',
        'subtotal'      => 'Subtotal',
        'discount'      => 'Descuento',
        'tax'           => 'Impuesto (:rate%)',
        'grand_total'   => 'Total general',
        'paid'          => 'Pagado',
        'amount_due'    => 'Importe pendiente',
        'generated'     => 'Generado :date · :app',
    ],

    // ─── Customer quote (public/quote/pdf.blade.php) ─────────────────
    'quote' => [
        'doc_title'          => 'Presupuesto :number',
        'label'              => 'PRESUPUESTO',
        'issued'             => 'Emitido :date',
        'valid_until'        => 'Válido hasta :date',
        'from'               => 'De',
        'to'                 => 'Para',
        'introduction'       => 'Introducción',
        'th_item'            => 'Concepto',
        'th_qty'             => 'Cant.',
        'th_unit'            => 'Unidad (:currency)',
        'th_total'           => 'Total (:currency)',
        'subtotal'           => 'Subtotal',
        'discount'           => 'Descuento',
        'tax'                => 'Impuesto (:rate%)',
        'grand_total'        => 'Total general',
        'terms_conditions'   => 'Términos y condiciones',
        'signed_by'          => 'por :name el :date (IP :ip)',
        'signed_label'       => 'Firmado',
        'generated'          => 'Generado :date · :app',
    ],

    // ─── Analytics PDF report (exports/pdf-report.blade.php) ─────────
    'report' => [
        'exported'      => 'Exportado: :time',
        'analytics'     => 'Analítica de :app',
        'period'        => 'Período: :from — :to',
        'filters'       => 'Filtros:',
        'no_data'       => 'No hay datos disponibles para el período seleccionado.',
        'footer'        => 'Generado por :brand · :time · Total de filas: :count',
    ],

];
