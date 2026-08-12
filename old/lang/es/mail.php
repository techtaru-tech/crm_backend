<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Líneas de asuntos y copia compartida de correos (Spanish)
|------------------------------------------------------------
| Accedidas mediante __('mail.<key>', [...]).
|
| Claves en snake_case, agrupadas por Mailable / archivo Blade.
| Todas las cadenas visibles para el usuario en
| resources/views/emails/* y app/Mail/*::envelope() pasan por
| aquí para mantener el cumplimiento del Item 1 de CodeCanyon
| (sin texto visible codificado).
*/

return [

    // ─── Shared layout (resources/views/emails/layout.blade.php) ──
    'layout_default_title'        => 'LeadHub',
    'layout_preheader_fallback'   => 'Notificación de :app',
    'layout_footer_default'       => 'Recibe este correo porque es usuario de :app.',

    // ─── Meeting booked (resources/views/emails/meeting/booked.blade.php) ──
    'meeting_booked_subject_host'        => 'Nueva reserva: :name con :guest el :when',
    'meeting_booked_subject_guest'       => 'Su reunión está confirmada: :name el :when',
    'meeting_booked_default_name'        => 'Reunión',
    'meeting_booked_title'               => 'Reunión confirmada',
    'meeting_booked_heading_host'        => 'Nueva reserva recibida',
    'meeting_booked_heading_guest'       => 'Su reunión está confirmada',
    'meeting_booked_label_when'          => 'Cuándo',
    'meeting_booked_label_guest'         => 'Invitado',
    'meeting_booked_label_phone'         => 'Teléfono',
    'meeting_booked_label_host'          => 'Anfitrión',
    'meeting_booked_label_location'      => 'Ubicación',
    'meeting_booked_label_notes'         => 'Notas',
    'meeting_booked_location_google_meet' => 'Google Meet (el enlace se enviará a continuación)',
    'meeting_booked_location_zoom'       => 'Zoom (el enlace se enviará a continuación)',
    'meeting_booked_location_phone'      => 'Llamada telefónica',
    'meeting_booked_location_in_person'  => 'En persona',
    'meeting_booked_location_default'    => 'Detalles a continuación',
    'meeting_booked_btn_reschedule'      => 'Reprogramar',
    'meeting_booked_btn_cancel'          => 'Cancelar',
    'meeting_booked_ics_note'            => 'Invitación de calendario (.ics) adjunta — ábrala para añadirla a su calendario.',

    // ─── Meeting cancelled (resources/views/emails/meeting/cancelled.blade.php) ──
    'meeting_cancelled_subject'   => 'Reunión cancelada: :name el :when',
    'meeting_cancelled_default_name' => 'Reunión',
    'meeting_cancelled_title'     => 'Reunión cancelada',
    'meeting_cancelled_body'      => 'La reunión originalmente programada para el :when (:tz) ha sido cancelada.',
    'meeting_cancelled_reason'    => 'Motivo:',
    'meeting_cancelled_book_again_intro' => '¿Necesita otra hora?',
    'meeting_cancelled_book_again_link'  => 'Reservar de nuevo',

    // ─── Portal magic link (resources/views/emails/portal-magic-link.blade.php) ──
    'portal_magic_link_subject'   => 'Su enlace de acceso al portal de :app',
    'portal_magic_link_greeting'  => 'Hola :name,',
    'portal_magic_link_default_name' => 'estimado/a',
    'portal_magic_link_body'      => 'Aquí tiene su enlace seguro de inicio de sesión. Haga clic en el botón siguiente para acceder a su cuenta. Este enlace es válido durante 30 minutos y solo puede utilizarse una vez.',
    'portal_magic_link_button'    => 'Iniciar sesión',
    'portal_magic_link_ignore'    => 'Si no solicitó este enlace, puede ignorar este correo con total seguridad.',
    'portal_magic_link_fallback'  => '¿El enlace no funciona? Péguelo en su navegador:',

    // ─── Tenant welcome (resources/views/emails/tenant-welcome.blade.php) ──
    'tenant_welcome_subject'      => 'Su espacio de trabajo :workspace está listo',
    'tenant_welcome_hello'        => 'Hola,',
    'tenant_welcome_intro'        => 'Su espacio de trabajo :workspace está listo en :app.',
    'tenant_welcome_user_set_password' => 'Puede iniciar sesión cuando quiera con el correo y la contraseña que eligió durante el registro.',
    'tenant_welcome_admin_created'     => 'Un administrador creó este espacio de trabajo para usted. Use el botón siguiente para establecer una contraseña e iniciar sesión por primera vez.',
    'tenant_welcome_workspace_label'   => 'Espacio de trabajo:',
    'tenant_welcome_email_label'       => 'Correo electrónico:',
    'tenant_welcome_button_set_password' => 'Establecer contraseña e iniciar sesión',
    'tenant_welcome_button_login'        => 'Iniciar sesión en su espacio de trabajo',
    'tenant_welcome_setup_expires'       => 'Este enlace de configuración es válido durante 60 minutos. Si caduca, simplemente use el',
    'tenant_welcome_forgot_password'     => '"Olvidé mi contraseña"',
    'tenant_welcome_setup_expires_suffix' => 'enlace en la página de inicio de sesión.',
    'tenant_welcome_ignore'              => 'Si no esperaba este correo, puede ignorarlo con total seguridad.',

    // ─── Invitation (resources/views/emails/invitation.blade.php) ──
    'invitation_subject'          => 'Ha sido invitado a :workspace en :app',
    'invitation_default_inviter' => 'Un miembro del equipo',
    'invitation_hello'           => 'Hola,',
    'invitation_body'            => ':inviter le ha invitado a unirse a :workspace en :app como :role.',
    'invitation_button'          => 'Aceptar invitación',
    'invitation_expiry'          => 'Esta invitación caducará en 7 días.',
    'invitation_ignore'          => 'Si no esperaba esta invitación, puede ignorar este correo con total seguridad.',

    // ─── Password reset (resources/views/emails/password-reset.blade.php) ──
    'password_reset_subject'     => 'Restablezca su contraseña de :app',
    'password_reset_default_name' => 'estimado/a',
    'password_reset_greeting'    => 'Hola :name,',
    'password_reset_intro'       => 'Hemos recibido una solicitud para restablecer la contraseña de su cuenta de :app. Haga clic en el botón siguiente para elegir una nueva.',
    'password_reset_button'      => 'Restablecer mi contraseña',
    'password_reset_expires'     => 'Este enlace caduca en :minutes minutos. Si no solicitó un restablecimiento de contraseña, puede ignorar este correo con total seguridad — su contraseña seguirá siendo la misma.',
    'password_reset_fallback'    => 'Si el botón anterior no funciona, pegue esta URL en su navegador:',

    // ─── Payment failed (resources/views/emails/payment-failed.blade.php) ──
    'payment_failed_subject'     => 'Acción necesaria: pago fallido para :workspace',
    'payment_failed_heading'     => 'Pago fallido',
    'payment_failed_attempt'     => 'Intento :attempt — por favor actualice su método de pago.',
    'payment_failed_greeting'    => 'Hola,',
    'payment_failed_body'        => 'Intentamos cobrar la tarjeta registrada para su suscripción de :workspace y el pago no se procesó.',
    'payment_failed_amount_label'      => 'Importe pendiente',
    'payment_failed_next_retry_label'  => 'Próximo reintento automático',
    'payment_failed_cta_body'    => 'Para evitar interrupciones, actualice su método de pago lo antes posible. Reintentaremos el cobro automáticamente una vez que actualice la tarjeta.',
    'payment_failed_button'      => 'Actualizar método de pago',
    'payment_failed_help'        => 'Motivos habituales por los que falla un cobro: tarjeta caducada, fondos insuficientes o un bloqueo antifraude del banco. Si necesita ayuda, responda a este correo.',

    // ─── Plan changed (resources/views/emails/plan-changed.blade.php) ──
    'plan_changed_subject_upgrade'   => 'Ha sido actualizado al plan :plan',
    'plan_changed_subject_downgrade' => 'Su plan ha cambiado a :plan',
    'plan_changed_subject_default'   => 'Su plan se ha actualizado a :plan',
    'plan_changed_heading_upgrade'   => 'Ahora está en el plan :plan',
    'plan_changed_heading_downgrade' => 'Plan actualizado a :plan',
    'plan_changed_heading_default'   => 'Plan actualizado a :plan',
    'plan_changed_greeting'      => 'Hola,',
    'plan_changed_body'          => 'Su plan para :workspace en :app ha sido actualizado.',
    'plan_changed_previous_label' => 'Plan anterior',
    'plan_changed_new_label'     => 'Nuevo plan',
    'plan_changed_upgrade_note'  => 'Las nuevas funciones y los límites ampliados ya están disponibles en todo su espacio de trabajo. Inicie sesión cuando quiera para aprovecharlas.',
    'plan_changed_downgrade_note' => 'Su nuevo plan está activo de inmediato. Algunas funciones de su plan anterior pueden dejar de estar disponibles — consulte la página de facturación para más detalles.',
    'plan_changed_button'        => 'Ver panel de facturación',

    // ─── Plan slug labels (Pass 22) ────────────────────────────────────
    // Used by plan-changed.blade.php to translate the old/new plan slug
    // shown in the previous-plan / new-plan rows. Unknown future plans
    // fall back to ucfirst() in the view.
    'plan_value_free'            => 'Gratis',
    'plan_value_starter'         => 'Inicial',
    'plan_value_pro'             => 'Pro',
    'plan_value_business'        => 'Empresa',
    'plan_value_enterprise'      => 'Corporativo',
    'plan_value_trial'           => 'Prueba',

    // ─── Billing cycle labels (Pass 22) ────────────────────────────────
    // Used by subscription-activated.blade.php to translate the cycle
    // slug (monthly|yearly) interpolated into subscription_activated_billing_cycle.
    'billing_cycle_monthly'      => 'Mensual',
    'billing_cycle_yearly'       => 'Anual',
    'billing_cycle_quarterly'    => 'Trimestral',

    // ─── Subscription activated (resources/views/emails/subscription-activated.blade.php) ──
    'subscription_activated_subject' => 'Bienvenido al plan :plan — todo listo',
    'subscription_activated_heading' => 'Bienvenido al plan :plan 🎉',
    'subscription_activated_greeting' => 'Hola,',
    'subscription_activated_body' => 'Su suscripción para :workspace en :app ya está activa. Todo lo que creó durante su prueba se conserva — leads, embudos, automatizaciones e integraciones.',
    'subscription_activated_billing_cycle' => 'Ciclo de facturación: :cycle.',
    'subscription_activated_button' => 'Ver panel de facturación',
    'subscription_activated_footer' => 'Si tiene cualquier pregunta sobre su plan, simplemente responda a este correo y nos encargaremos de ello.',

    // ─── Subscription cancelled (resources/views/emails/subscription-cancelled.blade.php) ──
    'subscription_cancelled_subject' => 'Su suscripción de :workspace ha sido cancelada',
    'subscription_cancelled_heading' => 'Su suscripción ha sido cancelada',
    'subscription_cancelled_greeting' => 'Hola,',
    'subscription_cancelled_intro'   => 'Hemos cancelado su suscripción para :workspace en :app.',
    'subscription_cancelled_ends_at' => 'Mantendrá acceso completo hasta el :date. Después de esa fecha, el espacio de trabajo se pausará y deberá reactivarlo para seguir usándolo.',
    'subscription_cancelled_immediate' => 'El acceso se ha pausado con efecto inmediato.',
    'subscription_cancelled_data_safe' => 'Sus datos — leads, embudos, automatizaciones — permanecen seguros en nuestros servidores. Si cambia de opinión en un plazo de 90 días, puede reactivar con un solo clic y continuar exactamente donde lo dejó.',
    'subscription_cancelled_reason'   => 'Motivo registrado: :reason',
    'subscription_cancelled_button'   => 'Reactivar suscripción',
    'subscription_cancelled_footer'   => 'Lamentamos verle marchar. Si hay algo que podríamos haber hecho mejor, responda a este correo y díganoslo.',

    // ─── Subscription expired (resources/views/emails/subscription-expired.blade.php) ──
    'subscription_expired_subject' => 'Su suscripción de :workspace ha caducado',
    'subscription_expired_heading' => 'Su suscripción ha caducado',
    'subscription_expired_greeting' => 'Hola,',
    'subscription_expired_body'    => 'Su suscripción para :workspace en :app ha caducado. El acceso al panel de administración se ha pausado, pero sus datos siguen aquí esperándole.',
    'subscription_expired_reactivate' => 'Reactive cuando esté listo para continuar donde lo dejó.',
    'subscription_expired_button'  => 'Reactivar suscripción',
    'subscription_expired_footer'  => '¿Tiene preguntas sobre facturación? Simplemente responda a este correo.',

    // ─── Trial ending soon (resources/views/emails/trial-ending-soon.blade.php) ──
    'trial_ending_soon_subject_tomorrow' => 'Su prueba de :workspace termina mañana',
    'trial_ending_soon_subject_days'     => 'Su prueba de :workspace termina en :days días',
    'trial_ending_soon_heading_one'  => 'Su prueba termina en :days día',
    'trial_ending_soon_heading_other' => 'Su prueba termina en :days días',
    'trial_ending_soon_greeting'    => 'Hola,',
    'trial_ending_soon_body'        => 'Solo un aviso amistoso — su prueba gratuita de :workspace en :app termina el :ends_at. Actualice ahora para mantener todos sus leads, embudos y automatizaciones funcionando sin interrupciones.',
    'trial_ending_soon_after'       => 'Cuando termine su prueba, el acceso al panel de administración se pausará hasta que elija un plan. No se eliminará ninguno de sus datos.',
    'trial_ending_soon_button'      => 'Elegir su plan',
    'trial_ending_soon_footer'      => '¿Preguntas? Simplemente responda a este correo y le ayudaremos a elegir el plan adecuado.',

    // ─── Trial expired (resources/views/emails/trial-expired.blade.php) ──
    'trial_expired_subject' => 'Su prueba de :workspace ha finalizado',
    'trial_expired_heading' => 'Su prueba ha finalizado',
    'trial_expired_greeting' => 'Hola,',
    'trial_expired_body'   => 'Su prueba gratuita de :workspace en :app ha finalizado. El acceso al panel de administración está pausado hasta que elija un plan — pero no se preocupe, todos sus leads, formularios y configuraciones están a salvo.',
    'trial_expired_pick_plan' => 'Elija un plan cuando esté listo y recuperará el acceso completo en segundos.',
    'trial_expired_button' => 'Reactivar su espacio de trabajo',
    'trial_expired_footer' => '¿Necesita ayuda para elegir? Responda a este correo — estaremos encantados de ayudarle.',

    // ─── Workspace suspended (resources/views/emails/workspace-suspended.blade.php) ──
    'workspace_suspended_subject' => 'Su espacio de trabajo :workspace ha sido suspendido',
    'workspace_suspended_heading' => 'Su espacio de trabajo ha sido suspendido',
    'workspace_suspended_greeting' => 'Hola,',
    'workspace_suspended_body'    => 'Su espacio de trabajo :workspace en :app ha sido suspendido tras una inactividad prolongada después del fin de su suscripción. Todos los miembros han sido desconectados del panel de administración.',
    'workspace_suspended_data_safe' => 'Sus datos están a salvo — leads, formularios, automatizaciones y configuraciones están todos preservados. Reactivar está a un clic de distancia: elija un plan y su equipo volverá a estar dentro en segundos.',
    'workspace_suspended_button'  => 'Reactivar su espacio de trabajo',
    'workspace_suspended_footer'  => 'Si esto parece un error o necesita ayuda para volver a entrar, simplemente responda a este correo y lo resolveremos.',

    // ─── Tenant erasure requested (resources/views/emails/tenant-erasure-requested.blade.php) ──
    'tenant_erasure_requested_subject' => 'Su espacio de trabajo :workspace será eliminado en :days días',
    'tenant_erasure_requested_heading' => 'Eliminación de espacio de trabajo programada',
    'tenant_erasure_requested_greeting' => 'Hola :name,',
    'tenant_erasure_requested_intro'   => 'Hemos recibido su solicitud para eliminar el espacio de trabajo :workspace en :app. Sus datos — cada lead, formulario, automatización, integración y configuración — serán borrados permanentemente en :days días. Esta acción no se puede deshacer una vez cerrada la ventana de espera.',
    'tenant_erasure_requested_window'  => 'Durante la ventana de :days días su espacio de trabajo está suspendido — el inicio de sesión está bloqueado, pero cada registro se conserva intacto por si cambia de opinión. Puede cancelar la eliminación en cualquier momento antes de que se cierre la ventana desde la página de Privacidad y Datos.',
    'tenant_erasure_requested_button'  => 'Cancelar eliminación',
    'tenant_erasure_requested_footer'  => '¿No solicitó esto? Haga clic en "Cancelar eliminación" arriba inmediatamente y contacte con soporte — bloquearemos el espacio de trabajo e investigaremos. Este mensaje cumple con nuestras obligaciones de notificación bajo el Artículo 17 del RGPD (Derecho al Olvido).',

    // ─── Test email (resources/views/emails/test.blade.php) ──
    'test_subject'    => 'Correo de prueba — :app',
    'test_heading'    => 'Prueba de configuración de correo',
    'test_greeting'   => 'Hola :name,',
    'test_body'       => 'Este es un correo de prueba de :app. Si ha recibido este mensaje, la configuración de su correo es correcta.',
    'test_continued'  => 'Ya puede enviar correos con su marca desde su espacio de trabajo.',
    'test_button'     => 'Abrir panel',

    // ─── Invoice send (app/Filament/Resources/InvoiceResource.php) ──
    'invoice_send_subject' => 'Factura :number',
    'invoice_send_body'    => "Hola :name,\n\nLa factura :number está lista: :url\n\nGracias.",

    // ─── Quote send (app/Filament/Resources/QuoteResource.php) ──
    'quote_send_subject'   => 'Presupuesto :number',
    'quote_send_body'      => "Hola :name,\n\nSu presupuesto está listo: :url\n\nSaludos cordiales,",

    // ─── Quote send for signature (app/Filament/Resources/QuoteResource/Pages/ViewQuote.php) ──
    'quote_send_review_subject' => 'Presupuesto :number — por favor revise',
    'quote_send_review_body'    => "Hola :name,\n\nSu presupuesto está listo para revisión y firma:\n:url\n\nGracias.",

    // ─── Notification digest (app/Console/Commands/SendNotificationDigest.php) ──
    'digest_subject'                  => 'Su resumen de notificaciones de :app — :datetime',
    'digest_heading'                  => 'Resumen de notificaciones de :app',
    'digest_intro_lede'               => 'Hola :name, esto es lo que se perdió en la última hora',
    'digest_col_type'                 => 'Tipo',
    'digest_col_details'              => 'Detalles',
    'digest_col_when'                 => 'Cuándo',
    'digest_view_button'              => 'Ver en :app',
    'digest_footer_explainer'         => 'Recibe esto porque configuró las notificaciones como resumen cada hora.',
    'digest_manage_preferences_link'  => 'Gestionar preferencias',
    'digest_fallback_message'         => 'Notificación',

    // ─── Meeting ICS fallbacks (app/Mail/MeetingBookedMail.php, MeetingCancelledMail.php) ──
    'meeting_default_name'   => 'Reunión',
    'host_default_name'      => 'Anfitrión',
    'meeting_description'    => 'Reunión con :host. Reprogramar o cancelar: :url',
    // Filename of the .ics attachment buyer sees in their email client.  Use a
    // safe-slug form (no spaces or punctuation other than dash) so all email
    // clients accept the filename unmodified.  Pass-33 i18n fix — without this
    // the English literal "meeting-" prefix leaked into non-EN buyer inboxes.
    'meeting_ics_filename'   => 'reunion',

    // ─── Onboarding drip series (app/Mail/OnboardingDripMail.php) ──
    'drip_day_1_heading'  => 'Bienvenido a bordo',
    'drip_day_1_body'     => "Nos alegra tenerle aquí. La forma más rápida de saber si este CRM se ajusta a su flujo de trabajo es añadir un solo lead y llevarlo de la bandeja de entrada hasta cerrado.\n\nLe llevará unos 90 segundos. Haga clic abajo y comencemos.",
    'drip_day_1_cta'      => 'Añadir mi primer lead',

    'drip_day_3_heading'  => '¿Cómo le está pareciendo?',
    'drip_day_3_body'     => "Dos días después. La mayoría de los equipos se atascan en uno de estos puntos:\n\n• Configurar las etapas del embudo correctas → Ajustes → Embudos\n• Conectar su correo existente → Ajustes → Correo\n• Importar leads desde una hoja de cálculo → Leads → Importar\n\nSi se ha topado con alguno de estos (u otra cosa), responda a este correo — leemos cada respuesta.",
    'drip_day_3_cta'      => 'Abrir mi panel',

    'drip_day_5_heading'  => 'Las 3 automatizaciones que todo equipo activa en la primera semana',
    'drip_day_5_body'     => "La mayoría de los CRM son pasivos — los leads se quedan ahí hasta que alguien se da cuenta. Estas tres automatizaciones se dan cuenta por usted:\n\n1. Asignar automáticamente nuevos leads por turno para que nada se escape\n2. Notificar a Slack sobre leads importantes para que los comerciales no tengan que refrescar\n3. Reactivar leads fríos después de 7 días con un correo amable de seguimiento\n\nLas tres se configuran en menos de 5 minutos.",
    'drip_day_5_cta'      => 'Explorar automatizaciones',

    'drip_day_7_heading'  => 'Una semana después — comprobación rápida',
    'drip_day_7_body'     => "¿Cómo va todo?\n\nSi el CRM ya se está pagando solo (ha añadido leads, su equipo lo está usando, está cerrando tratos que de otro modo habría perdido): perfecto — su prueba se convierte automáticamente en un plan de pago cuando termine, sin necesidad de hacer nada.\n\nSi sigue dudando: responda a este correo con lo que le falta. Hemos lanzado 14 funciones a partir de comentarios de cancelaciones en los últimos 6 meses.",
    'drip_day_7_cta'      => 'Ver planes',

    'drip_default_heading' => 'Una nota de su CRM',
    'drip_default_body'    => 'Esperamos que esté encontrando todo útil hasta ahora.',
    'drip_default_cta'     => 'Abrir mi panel',

];
