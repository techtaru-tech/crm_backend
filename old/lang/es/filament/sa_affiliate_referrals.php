<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin AffiliateReferralResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_affiliate_referrals.<key>').
*/

return [
    'nav_label'            => 'Comisiones de afiliados',
    'model_label'          => 'Comisión de afiliado',
    'plural_model_label'   => 'Comisiones de afiliados',

    // Table columns
    'col_affiliate'        => 'Afiliado',
    'col_referred'         => 'Espacio recomendado',
    'col_plan'             => 'Plan',
    'col_commission'       => 'Comisión',
    'col_rate'             => 'Tasa',
    'col_status'           => 'Estado',
    'col_booked'           => 'Registrada',
    'col_paid_at'          => 'Pagada el',

    // Filter
    'filter_status'        => 'Estado',

    // Row actions
    'action_approve'       => 'Aprobar',
    'action_mark_paid'     => 'Marcar como pagada',
    'action_reverse'       => 'Revertir',

    // Row-action notifications
    'notify_approved'      => 'Comisión aprobada.',
    'notify_paid'          => 'Comisión marcada como pagada.',
    'notify_reversed'      => 'Comisión revertida.',

    // Bulk actions
    'bulk_approve'         => 'Aprobar seleccionadas',
    'bulk_mark_paid'       => 'Marcar seleccionadas como pagadas',
    'notify_bulk_approved' => ':count comisión(es) aprobada(s).',
    'notify_bulk_paid'     => ':count comisión(es) marcada(s) como pagada(s).',

    // Empty state
    'empty_heading'        => 'Aún no hay comisiones de afiliados',
    'empty_description'    => 'Las comisiones se registran automáticamente cuando un espacio recomendado realiza un pago.',
];
