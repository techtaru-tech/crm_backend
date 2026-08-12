<?php

declare(strict_types=1);

return [

    // ─── Layout ────────────────────────────────────────────────────
    'quote_label_prefix'       => 'Presupuesto :number',
    'valid_until_prefix'       => 'Válido hasta :date',
    'grand_total'              => 'Total general',

    // ─── Píldoras de estado ────────────────────────────────────────
    'status_accepted'          => 'Aceptado',
    'status_declined'          => 'Rechazado',
    'status_expired'           => 'Caducado',
    'status_awaiting_review'   => 'Esperando su revisión',
    'status_draft'             => 'Borrador',
    'status_sent'              => 'Enviado',
    'status_converted'         => 'Convertido',

    // ─── Tabla de conceptos ────────────────────────────────────────
    'items_heading'            => 'Conceptos',
    'col_item'                 => 'Concepto',
    'col_qty'                  => 'Cant.',
    'col_unit'                 => 'Unitario',
    'col_total'                => 'Total',
    'subtotal'                 => 'Subtotal',
    'discount'                 => 'Descuento',
    'tax_label'                => 'Impuesto (:rate%)',
    'total'                    => 'Total',
    'terms_conditions'         => 'Términos y condiciones',
    'page_title'               => 'Presupuesto :number',

    // ─── Formulario de respuesta ───────────────────────────────────
    'your_response'            => 'Su respuesta',
    'type_full_name_to_sign'   => 'Escriba su nombre completo para firmar',
    'name_placeholder'         => 'p. ej. Juana Pérez',
    'agree_legal_text'         => 'Acepto los términos anteriores. Al hacer clic en Aceptar proporciono una firma escrita legalmente vinculante. Mi dirección IP (:ip) y la fecha/hora se registrarán con fines de auditoría.',
    'accept_and_sign'          => 'Aceptar y firmar',
    'decline_with_reason'      => 'Rechazar con un motivo',
    'decline_placeholder'      => 'Indíquenos el motivo (opcional, pero útil)…',
    'decline'                  => 'Rechazar',
    'download_pdf'             => 'Descargar PDF',
    'no_longer_accepting'      => 'Este presupuesto ya no admite respuestas.',
    'secured_link_suffix'      => 'Enlace seguro — :app',

    // ─── Página de aceptado ────────────────────────────────────────
    'accepted_title_suffix'    => 'Presupuesto :number — Aceptado',
    'accepted_heading'         => '¡Gracias!',
    'accepted_quote_label'     => 'Presupuesto :number',
    'accepted_recorded_body'   => 'Su aceptación se ha registrado con una firma marcada con fecha/hora y dirección IP.',
    'accepted_next_step'       => 'Próximo paso',
    'accepted_invoice_body'    => 'Se ha creado la Factura :number para este pedido. Puede pagarla aquí:',
    'accepted_view_pay_invoice'=> 'Ver y pagar Factura',
    'accepted_what_next'       => 'Qué sucede a continuación',
    'accepted_invoice_pending' => 'Su factura le será enviada en breve.',
];
