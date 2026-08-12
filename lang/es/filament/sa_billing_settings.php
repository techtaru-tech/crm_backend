<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin BillingSettingsPage — Filament admin strings
|------------------------------------------------------------
*/

return [

    // ----- Page title -----
    'title'                       => 'Pasarela de pago y configuración del ciclo de vida',
    'navigation_label'            => 'Pago y ciclo de vida',
    'tabs_outer'                  => 'Pasarelas',

    // ----- One-time cron setup -----
    'cron_section_heading'        => '⚙️ Configuración única del servidor',
    'cron_section_description'    => 'Para que los recordatorios de prueba, los correos de expiración y la copia de seguridad diaria se ejecuten realmente, su servidor necesita una sola entrada cron. Añádala una vez y todas las tareas programadas del script se activarán automáticamente.',
    'cron_setup_step_label'       => 'Añada esta línea a los Cron Jobs del panel de su hosting (cPanel / Plesk / DirectAdmin), o al crontab de su servidor:',
    'cron_setup_step_thats_it'    => 'Eso es todo: configúrelo una vez y olvídese. El script gestiona todo lo demás internamente (cuándo enviar cada correo, cuándo expirar las pruebas, cuándo hacer copias de seguridad, etc.).',
    'cron_setup_step_support'     => 'Si no está seguro de cómo añadir una entrada cron, el soporte de su proveedor de hosting puede hacerlo por usted en menos de un minuto: solo tiene que enviarles la línea de arriba.',

    // ----- Trial & lifecycle section -----
    'trial_lifecycle_description' => 'Controla cuánto duran las pruebas, cuándo se envían los recordatorios antes / después de la expiración, y si los inquilinos expirados se suspenden automáticamente tras un período de gracia. ProcessSubscriptionLifecycle lee estos valores en cada ejecución cron: no se necesita reiniciar la aplicación.',
    'trial_days_label'            => 'Duración predeterminada de la prueba (días)',
    'trial_days_suffix'           => 'días',
    'trial_days_helper'           => 'Se utiliza cuando el Plan elegido no tiene trial_days propio. Cada Plan puede sobrescribir este valor desde la página de Planes.',
    'trial_reminder_days_label'   => 'Cadencia de recordatorios de prueba (días ANTES de que termine la prueba)',
    'reminder_placeholder'        => 'Escriba el número de días y pulse Intro',
    'trial_reminder_days_helper'  => 'p. ej. 7, 3, 1 → correos enviados 7, 3 y 1 día antes de trial_ends_at. Cada número entero es un recordatorio. Vacío = sin recordatorios.',
    'post_expiry_reminder_days_label' => 'Cadencia de goteo tras la expiración (días DESPUÉS de la expiración)',
    'post_expiry_reminder_days_helper' => 'p. ej. 3, 7, 14 → correos de goteo enviados 3, 7 y 14 días después de la expiración para recuperar inquilinos caducados. Vacío = sin goteo.',
    'auto_suspend_after_label'    => 'Suspensión automática tras (días tras la expiración)',
    'auto_suspend_after_helper'   => '0 = nunca suspender automáticamente. De lo contrario, los inquilinos expirados pasan a active=false estos días después de su fecha de expiración y reciben un correo de aviso final.',

    // ----- Enabled gateways section -----
    'enabled_gateways_description'=> 'Solo las pasarelas que marque aquí (y que tengan las credenciales completas más abajo) se ofrecerán a los inquilinos en la página de precios.',
    'field_enabled_gateways_label'=> 'Pasarelas habilitadas',
    'gateway_stripe'              => 'Stripe (Tarjeta)',
    'gateway_paypal'              => 'PayPal',
    'gateway_razorpay'            => 'Razorpay',
    'gateway_paystack'            => 'Paystack',
    'gateway_manual'              => 'Transferencia bancaria (Manual)',

    // ----- Stripe tab -----
    'tab_stripe'                  => 'Stripe',
    'test_mode'                   => 'Modo prueba',
    'stripe_publishable_key'      => 'Clave publicable',
    'stripe_secret_key'           => 'Clave secreta',
    'webhook_signing_secret'      => 'Secreto de firma del Webhook',
    'stripe_webhook_helper'       => 'Opcional, pero recomendado. Apunte su Webhook de Stripe a :url',

    // ----- PayPal tab -----
    'tab_paypal'                  => 'PayPal',
    'sandbox_mode'                => 'Modo sandbox',
    'paypal_client_id'            => 'ID de cliente',
    'paypal_client_secret'        => 'Secreto de cliente',
    'paypal_webhook_id'           => 'ID del Webhook',
    'paypal_webhook_helper'       => 'Endpoint del Webhook: :url',

    // ----- Razorpay tab -----
    'tab_razorpay'                => 'Razorpay',
    'razorpay_key_id'             => 'ID de clave',
    'razorpay_key_secret'         => 'Secreto de clave',
    'razorpay_webhook_secret'     => 'Secreto del Webhook',
    'razorpay_webhook_helper'     => 'Endpoint del Webhook: :url',

    // ----- Paystack tab -----
    'tab_paystack'                => 'Paystack',
    'paystack_public_key'         => 'Clave pública',
    'paystack_secret_key'         => 'Clave secreta',

    // ----- Manual bank transfer tab -----
    'tab_manual_bank'             => 'Transferencia bancaria manual',
    'manual_bank_name'            => 'Nombre del banco',
    'manual_account_name'         => 'Nombre de la cuenta',
    'manual_account_number'       => 'Número de cuenta',
    'manual_iban'                 => 'IBAN',
    'manual_swift'                => 'SWIFT / BIC',
    'manual_extra_instructions'   => 'Instrucciones adicionales',
    'manual_extra_helper'         => 'Se muestra en la página de instrucciones de transferencia después de que un inquilino elija esta pasarela.',

    // ----- Notifications -----
    'settings_saved'              => 'Configuración guardada.',
    'no_gateway_configured'       => 'Aún no hay ninguna pasarela habilitada y totalmente configurada.',
    'active_gateways'             => 'Pasarelas activas: :labels',
    'stripe_mismatch_title'       => 'Discrepancia del modo de prueba de Stripe',
    'stripe_mismatch_body'        => 'El conmutador «Modo prueba» está :toggle pero el prefijo de la clave secreta indica :prefix. Stripe enruta por el prefijo de la clave, no por el conmutador: cambie uno de los dos para que coincidan.',
    'toggle_on'                   => 'ACTIVADO',
    'toggle_off'                  => 'DESACTIVADO',

    // ----- Header actions -----
    'save_settings'               => 'Guardar configuración',

    // ----- Hero -----
    'hero_eyebrow'                => 'Facturación',
    'hero_title'                  => 'Configurar pasarelas de pago',
    'body_intro'                  => 'Despliegue una o varias pasarelas a la vez. Cada inquilino elige su método preferido en la página de precios. Stripe y PayPal cubren la mayor parte del tráfico global; Razorpay y Paystack son excelentes para India y África respectivamente; la transferencia bancaria manual funciona en cualquier lugar y es ideal para la facturación empresarial.',

    // Affiliate program
    'affiliate_section_heading'     => 'Programa de afiliados',
    'affiliate_section_description' => 'Comisión pagada a los inquilinos que recomiendan nuevos espacios de trabajo de pago. Se aplica a cada pago recurrente de un espacio recomendado; revise y pague las comisiones en Facturación → Comisiones de afiliados.',
    'affiliate_commission_label'    => 'Tasa de comisión',
    'affiliate_commission_helper'   => 'Porcentaje de cada pago recomendado registrado como comisión de afiliado. Establezca 0 para desactivar el programa de afiliados.',
];
