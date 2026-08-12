<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| BillingPortal — Filament tenant strings (es)
|------------------------------------------------------------
| Accessed via __('filament/billing_portal.<key>').
*/

return [
    'title'                          => 'Facturación y suscripción',
    'heading'                        => 'Facturación y suscripción',
    'navigation_label'               => 'Facturación',

    // Subscription state subheadings
    'subheading_on_trial'            => 'Está en una prueba gratuita. Puede actualizar en cualquier momento — sus datos se conservan.',
    'subheading_active_paid'         => 'Su suscripción está activa.',
    'subheading_trial_expired'       => 'Su prueba ha terminado. Elija un plan para continuar.',
    'subheading_expired'             => 'Su suscripción ha caducado. Renuévela para recuperar el acceso completo.',
    'subheading_cancelled'           => 'Su suscripción está cancelada. Reactívela en cualquier momento.',
    'subheading_suspended'           => 'Este espacio de trabajo está suspendido. Contacte con soporte.',

    // View data defaults
    'state_unknown_label'            => 'Desconocido',

    // Deletion flow
    'type_delete_title'              => 'Escriba DELETE para confirmar.',
    'type_delete_body'               => 'La eliminación del espacio de trabajo es destructiva. Escriba la palabra DELETE en el campo de confirmación para continuar.',
    'no_workspace_title'             => 'Sin contexto de espacio de trabajo.',
    'auth_required_title'            => 'Autenticación requerida.',
    'owner_only_title'               => 'Solo el propietario.',
    'owner_only_body'                => 'Solo el propietario del espacio de trabajo puede programar la eliminación.',
    'password_mismatch_title'        => 'La contraseña no coincide.',
    'password_mismatch_body'         => 'Vuelva a introducir la contraseña de su cuenta para confirmar la eliminación del espacio de trabajo.',
    'totp_mismatch_title'            => 'El código de dos factores no coincide.',
    'totp_mismatch_body'             => 'Introduzca un código nuevo de 6 dígitos desde su aplicación autenticadora.',
    'deletion_scheduled_title'       => 'Eliminación del espacio de trabajo programada',
    'deletion_scheduled_body'        => 'Este espacio de trabajo se eliminará permanentemente en :days días. Hasta entonces puede cancelarlo desde esta página o desde Privacidad y datos.',
    'deletion_cancelled_title'       => 'Eliminación cancelada',
    'deletion_cancelled_body'        => 'Su espacio de trabajo permanecerá activo.',

    // Billing details save
    'review_details_title'           => 'Revise los datos de facturación.',
    'review_details_default_body'    => 'Algunos campos son inválidos.',
    'billing_country_regex'          => 'El país debe ser un código ISO-3166-1 alfa-2 (p. ej. US, DE, GB).',
    'no_changes_title'               => 'No hay cambios que guardar.',
    'billing_details_saved_title'    => 'Datos de facturación guardados.',
    'billing_details_saved_body'     => 'Aparecerán en cada recibo futuro.',

    // Subscription event descriptors
    'event_trial_ends'               => 'Fin de la prueba',
    'event_next_renewal'             => 'Próxima renovación',
    'event_trial_ended'              => 'Prueba finalizada',
    'event_subscription_ended'       => 'Suscripción finalizada',
    'event_access_ends'              => 'Fin del acceso',

    // Gateway labels
    'gateway_stripe'                 => 'Stripe',
    'gateway_paypal'                 => 'PayPal',
    'gateway_razorpay'               => 'Razorpay',
    'gateway_paystack'               => 'Paystack',
    'gateway_manual'                 => 'Transferencia bancaria',

    // ─── Blade view — billing portal page ─────────────────────────────
    'error_no_workspace'             => 'No pudimos resolver su espacio de trabajo. Por favor, cierre sesión y vuelva a iniciar sesión.',
    'cta_choose_plan'                => 'Elegir un plan',
    'section_current_plan'           => 'Plan actual',
    'price_free'                     => 'Gratis',
    'seat_team_seats'                => 'Asientos del equipo',
    'seat_limit_reached'             => 'Ha alcanzado el límite de asientos. Actualice para invitar a más miembros.',
    'features_whats_included'        => 'Lo que incluye',

    // ─── Feature labels (Pass 22) ─────────────────────────────────────
    'feature_integrations'           => 'Integraciones',
    'feature_automations'            => 'Automatizaciones',
    'feature_api_access'             => 'Acceso a la API',
    'feature_custom_domain'          => 'Dominio personalizado',
    'feature_white_label'            => 'Marca blanca',
    'feature_webhooks_outbound'      => 'Webhooks salientes',
    'feature_reports_advanced'       => 'Informes avanzados',
    'feature_priority_support'       => 'Soporte prioritario',
    'feature_marketplace_install'    => 'Instalaciones del marketplace',
    'feature_team_collaboration'     => 'Colaboración en equipo',
    'feature_unlimited_leads'        => 'Clientes potenciales ilimitados',
    'feature_sso'                    => 'Inicio de sesión único',
    'no_plan_information'            => 'No hay información del plan disponible.',
    'section_manage_subscription'    => 'Administrar suscripción',
    'gateway_paying_via_prefix'      => 'Pagando vía',
    'action_change_plan'             => 'Cambiar plan',
    'action_update_payment_method'   => 'Actualizar método de pago y facturas',
    'action_cancel_subscription'     => 'Cancelar suscripción',
    'support_hint'                   => '¿Necesita ayuda? Contacte con soporte — gestionaremos los cambios de facturación por usted en menos de 24 horas.',
    'section_recent_activity'        => 'Actividad reciente',
    'event_subscription_activated'   => 'Suscripción activada',
    'event_subscription_cancelled'   => 'Suscripción cancelada',
    'event_payment_failed'           => 'Pago fallido',
    'event_plan_changed'             => 'Plan cambiado',
    'event_notification_sent'        => 'Notificación enviada',
    'event_workspace_suspended'      => 'Espacio de trabajo suspendido',
    'event_workspace_reactivated'    => 'Espacio de trabajo reactivado',
    'event_auto_suspended'           => 'Suspendido automáticamente (tras caducidad)',
    'section_available_plans'        => 'Planes disponibles',
    'toggle_monthly'                 => 'Mensual',
    'toggle_annual'                  => 'Anual',
    'toggle_annual_save_badge'       => 'ahorre hasta un 20 %',
    'plan_tag_recommended'           => 'Recomendado',
    'plan_tag_current'               => 'Actual',
    'price_suffix_per_month'         => '/mes',
    'price_suffix_per_year'          => '/año',
    'plan_save_vs_monthly'           => 'Ahorre un :pct% frente al mensual',
    'preview_upgrade_strong'         => 'Cambie hoy, pague solo la diferencia:',
    'preview_charge_now_label'       => 'Cobrar ahora:',
    'preview_credit_applied_label'   => 'Crédito aplicado:',
    'preview_prorated_days_one'      => ':count día del plan actual',
    'preview_prorated_days_other'    => ':count días del plan actual',
    'preview_downgrade_strong'       => 'Bajar de plan con crédito:',
    'preview_account_credit_label'   => 'Crédito de la cuenta:',
    'preview_applied_next_invoice'   => 'Aplicado automáticamente a su próxima factura.',
    'plan_action_switch'             => 'Cambiar',
    'plan_action_switch_annual'      => 'Cambiar (anual)',
    'plan_active_label'              => 'Activo',
    'section_billing_details'        => 'Datos de facturación',
    'billing_details_hint'           => 'Se usan en cada PDF de recibo. Requeridos por equipos de compras / contabilidad en la mayoría de jurisdicciones.',
    'form_business_name_label'       => 'Razón social registrada',
    'form_vat_number_label'          => 'Número de IVA / GST',
    'form_country_label'             => 'País (ISO-3166-1 alfa-2)',
    'form_country_placeholder'       => 'US',
    'form_billing_address_label'     => 'Dirección de facturación',
    'form_billing_email_label'       => 'Correo de facturación (buzón de cuentas / contabilidad)',
    'form_billing_email_placeholder' => 'ap@example.com',
    'form_save_button'               => 'Guardar datos de facturación',

    // ─── Connector + interval fallback ───
    'of_connector'                   => 'de',
    'interval_month_fallback'        => 'mes',

    // ─── Interval labels (slug→localized) ─────────────────────────────
    'interval_month'                 => 'mes',
    'interval_year'                  => 'año',
    'interval_week'                  => 'semana',
    'interval_day'                   => 'día',
];
