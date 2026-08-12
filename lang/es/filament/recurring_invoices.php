<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — Cadenas de traducción de RecurringInvoiceResource
|--------------------------------------------------------------------------
|
| CRUD de "Facturas recurrentes / Cuotas" del tenant. Se consume mediante
| __('filament/recurring_invoices.<key>').
|
*/

return [

    'nav_label'           => 'Facturas recurrentes',
    'model_label'         => 'Factura recurrente',
    'plural_model_label'  => 'Facturas recurrentes',

    // Form
    'section_schedule'      => 'Programación recurrente',
    'section_schedule_desc' => 'Un cargo fijo mensual o anual. LeadHub crea una factura real en cada fecha de ejecución y te avisa cuando vence.',
    'field_lead'            => 'Miembro / Cliente',
    'field_company'         => 'Empresa (opcional)',
    'field_title'           => 'Descripción',
    'field_amount'          => 'Importe',
    'field_currency'        => 'Moneda',
    'field_interval'        => 'Factura cada',
    'interval_month'        => 'Mes',
    'interval_year'         => 'Año',
    'field_anchor_day'      => 'Día de facturación del mes',
    'field_anchor_day_help' => 'Opcional. 1-28. La próxima fecha de ejecución se ajusta a este día cada periodo.',
    'field_next_run_date'   => 'Próxima fecha de ejecución',
    'field_due_days'        => 'Vence después de (días)',
    'field_due_days_help'   => 'Días tras los que cada factura generada vence.',
    'field_auto_send'       => 'Enviar cada factura automáticamente',
    'field_auto_send_help'  => 'Marca cada factura generada como Enviada en lugar de Borrador.',
    'field_active'          => 'Activa',
    'field_notes'           => 'Notas',

    // Table
    'col_title'    => 'Descripción',
    'col_member'   => 'Miembro',
    'col_amount'   => 'Importe',
    'col_interval' => 'Intervalo',
    'col_next_run' => 'Próxima ejecución',
    'col_active'   => 'Activa',
];
