<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin ScriptSettings — Cadenas de Filament admin
|------------------------------------------------------------
| Accedido mediante __('filament/sa_script_settings.<key>').
| Los compradores traducen o adaptan editando este archivo o copiándolo
| a lang/<locale>/filament/sa_script_settings.php.
*/

return [

    // ----- Título de página / navegación -----
    'title'                            => 'Configuración de Script',
    'navigation_label'                 => 'Configuración de Script',

    // ----- Sección de informes y localización -----
    'reporting_section_description'    => 'Valores predeterminados utilizados por el panel de facturación, la página de precios y en cualquier lugar donde un tenant no haya elegido su propio valor. Se almacenan en la carga útil de la base de datos de Spatie Laravel Settings — sobreviven a reescrituras de .env y surten efecto inmediatamente al guardar (no se requiere limpieza de caché).',
    'reporting_currency_label'         => 'Moneda de informes',
    'reporting_currency_helper'        => 'Utilizada para las cifras de MRR / ARR / ARPU en el panel de facturación.',
    'default_timezone_label'           => 'Zona horaria predeterminada',
    'date_format_label'                => 'Formato de fecha',
    'default_language_label'           => 'Idioma predeterminado',
    'default_language_helper'          => 'Establece el idioma de la interfaz predeterminado para nuevos visitantes. Incluidos: inglés, árabe (RTL), español, hindi.',

    // Etiqueta de último recurso cuando config('locales.supported') está vacío —
    // aún necesitamos al menos una opción de Select, así que recurrimos a
    // 'en' => __('locale_fallback_english_native'). Permanece "English" en
    // el propio locale en, intencionalmente autorreferencial.
    'locale_fallback_english_native'   => 'English',

    // ----- Sección de copias de seguridad -----
    'backups_section_description'      => 'Copia de seguridad nocturna automática de la base de datos y los archivos subidos. Las copias de seguridad se almacenan en storage/app/backups/.',
    'auto_nightly_backup_label'        => 'Habilitar copias de seguridad nocturnas automáticas',
    'auto_nightly_backup_helper'       => 'Cuando está habilitado, se crea una copia de seguridad nueva cada noche a las 02:00 UTC mediante el ejecutor de tareas programadas (cron.php).',

    // ----- Sección de página de aterrizaje pública -----
    'landing_section_description'      => 'Qué página de aterrizaje ven los visitantes en la URL raíz (/). El cambio surte efecto después de guardar — no se requiere limpieza de caché.',
    'landing_template_label'           => 'Plantilla de aterrizaje',
    'landing_template_helper'          => 'Previsualice cualquier variante con /?preview=1 mientras está conectado como super-admin. El cambio surte efecto inmediatamente después de guardar — no se requiere limpieza de caché.',
    'landing_variant_light'            => 'Clara — héroe de marketing blanco pulido (predeterminado)',
    'landing_variant_warm'             => 'Cálida — fondo crema, tipografía serif, estilo revista editorial',
    'landing_variant_modern'           => 'Moderna — tema oscuro, gradiente púrpura/rosa',
    'landing_variant_editorial'        => 'Editorial — monocromático oscuro, bento estilo post-Linear',
    'landing_variant_classic'          => 'Clásica — plantilla original con mucho contenido',

    // ----- Sección de correo saliente / SMTP -----
    'smtp_section_description'         => 'Valores predeterminados de correo a nivel de script escritos en su archivo .env (config:clear se activa al guardar). Orden de precedencia en el envío: (1) SMTP propio del tenant desde la Configuración de correo → (2) estos valores .env a nivel de script → (3) controlador "log" codificado de respaldo de Laravel. Los tenants que dejen su página vacía recurren automáticamente a estos valores predeterminados.',
    'mailer_label'                     => 'Mailer',
    'mailer_helper'                    => 'Mantenga esto en "Log" mientras aún esté probando. Cambie a SMTP una vez que sus credenciales a continuación funcionen.',
    'mailer_option_smtp'               => 'SMTP',
    'mailer_option_sendmail'           => 'sendmail',
    'mailer_option_log'                => 'Log (solo desarrollo — no se envía correo real)',
    'mailer_option_array'              => 'Array (pruebas — descarta el correo)',
    'smtp_host_label'                  => 'Servidor SMTP',
    'smtp_host_placeholder'            => 'smtp.example.com',
    'smtp_port_label'                  => 'Puerto SMTP',
    'smtp_port_placeholder'            => '587 (STARTTLS), 465 (SSL), 25 (sin cifrar)',
    'encryption_label'                 => 'Cifrado',
    'encryption_option_tls'            => 'TLS (STARTTLS — recomendado)',
    'encryption_option_ssl'            => 'SSL',
    'encryption_option_none'           => 'Ninguno',
    'smtp_username_label'              => 'Usuario SMTP',
    'smtp_password_label'              => 'Contraseña SMTP',
    'smtp_password_placeholder'        => 'Deje sin cambios para mantener la actual',
    'from_address_label'               => 'Dirección De',
    'from_address_placeholder'         => 'noreply@yourdomain.com',
    'from_name_label'                  => 'Nombre De',
    'from_name_placeholder'            => 'LeadHub',

    // ----- Notificaciones -----
    'settings_saved_title'             => 'Configuración de script guardada.',
    'settings_saved_body'              => 'Valores predeterminados de informes y correo actualizados. Si cambió el mailer, envíe un correo de prueba para confirmar que funciona.',
    'no_user_title'                    => 'No hay usuario autenticado.',
    'mailer_dry_warning_title'         => 'El mailer actual es ":mailer" — no se enviará correo real.',
    'mailer_dry_warning_body'          => 'Cambie a SMTP y guarde antes de probar.',
    'test_email_sent_title'            => 'Correo de prueba enviado a :recipient',
    'test_email_sent_body'             => 'Revise la bandeja de entrada (y spam) en el próximo minuto.',
    'test_send_failed_title'           => 'Envío fallido: :error',
    'test_send_failed_body'            => 'Verifique de nuevo el servidor, puerto, cifrado y credenciales.',

    // ----- Cuerpo del correo de prueba -----
    'test_email_body_intro'            => 'Este es un correo de prueba desde la página de Configuración de Script de su :app.',
    'test_email_body_confirmation'     => 'Si recibió esto, sus credenciales SMTP están funcionando.',
    'test_email_body_sent_at'          => 'Enviado a las: :timestamp UTC',
    'test_email_subject'               => '[:app] Correo de prueba SMTP',

    // ----- Acciones de cabecera -----
    'action_send_test'                 => 'Enviar correo de prueba',
    'action_send_test_tooltip'         => 'Utiliza los valores actualmente en el formulario — pruebe primero, guarde si funciona.',
    'action_send_test_recipient_label' => 'Enviar correo de prueba a',
    'action_send_test_recipient_helper' => 'Cualquier dirección — anule para verificar la entrega a su buzón de producción antes de confirmar credenciales.',
    'action_send_test_modal_submit'    => 'Enviar prueba',
    'action_save_settings'             => 'Guardar configuración',

    // ----- Hero -----
    'page_hero_title'                  => 'Valores predeterminados globales para su despliegue de LeadHub',
    'hero_eyebrow'                     => 'Configuración del Propietario del Script',
    'hero_subtitle'                    => 'Elija la moneda de informes para su panel de facturación, su zona horaria predeterminada y el idioma con el que cada nuevo espacio de trabajo comienza.',

    // ----- Explicador del programador / cron -----
    'cron_details_summary'             => '¿Qué ejecuta el programador?',
    'cron_desc'                        => 'Configure su programador remoto para hacer GET a esta URL cada minuto:',

    // ─── Encabezado e introducción de la sección Cron ───
    'cron_section_title'               => 'Programador de tareas en segundo plano (cron)',
    'cron_section_desc_html'           => 'LeadHub ejecuta varias tareas en segundo plano cada minuto — envío de correo, automatizaciones, informes programados, puntuación de clientes potenciales, recordatorios de tareas, sondeo de bandeja IMAP, comprobaciones del ciclo de vida de suscripciones y copias de seguridad nocturnas. Todas ellas dependen de un disparador cron que llame a <code class="ss-chip">cron.php</code> o ejecute <code class="ss-chip">artisan schedule:run</code> una vez por minuto. Elija la opción que admita su servidor.',

    // ─── Lista de detalles del programador (intervalo → descripción) ───
    'cron_list_every_5_min_label'      => 'Cada 5 min',
    'cron_list_every_5_min_desc'       => 'sondeo de bandeja IMAP, recordatorios de tareas',
    'cron_list_every_15_min_label'     => 'Cada 15 min',
    'cron_list_every_15_min_desc'      => 'despachador de pasos de secuencias de correo',
    'cron_list_every_hour_label'       => 'Cada hora',
    'cron_list_every_hour_desc'        => 'automatizaciones sin actividad, entrega de informes programados, alertas de filtros guardados',
    'cron_list_every_6_hours_label'    => 'Cada 6 horas',
    'cron_list_every_6_hours_desc'     => 'ciclo de vida de suscripciones (vencimientos de prueba, recordatorios de período de gracia)',
    'cron_list_daily_02_label'         => 'Diariamente a las 02:00',
    'cron_list_daily_02_desc'          => 'copias de seguridad de bases de datos de tenants (cuando esté habilitado)',
    'cron_list_daily_09_label'         => 'Diariamente a las 09:00',
    'cron_list_daily_09_desc'          => 'ping de comprobación de actualizaciones de LeadHub',
    'cron_list_daily_user_label'       => 'Diariamente a la hora configurada por el usuario',
    'cron_list_daily_user_desc'        => 'resumen de notificaciones',

    // ─── Cron Opción A: Hosting compartido ───
    'cron_option_a_label'              => 'Hosting compartido (cPanel / Plesk / DirectAdmin)',
    'cron_option_a_tag'                => 'Más fácil',
    'cron_option_a_desc_html'          => 'En su panel de hosting, abra <em>Tareas Cron</em> y añada una tarea que se ejecute cada minuto:',
    'cron_option_a_hint_html'          => 'Algunos hostings compartidos bloquean <code class="ss-chip-light">exec()</code> — utilice la Opción B en su lugar si lo anterior falla silenciosamente.',

    // ─── Cron Opción B: Cron basado en URL ───
    'cron_option_b_label'              => 'Cron basado en URL (cron-job.org, EasyCron, UptimeRobot)',
    'cron_option_b_secret_hint_html'   => 'El parámetro <code class="ss-chip-light">secret=</code> debe coincidir con su <code class="ss-chip-light">CRON_SECRET</code> en <code class="ss-chip-light">.env</code> — esto bloquea que cualquier otra persona active su programador.',
    'cron_option_b_warn_html'          => '<strong>Atención:</strong> <code class="ss-chip-warn">CRON_SECRET</code> no está configurado — su disparador de URL está abierto a Internet. Añada un secreto aleatorio de 32 caracteres a <code class="ss-chip-warn">.env</code> y anexe <code class="ss-chip-warn">?secret=…</code> a la URL anterior.',

    // ─── Cron Opción C: VPS / dedicado (programador nativo de Laravel) ───
    'cron_option_c_label'              => 'VPS / dedicado (programador nativo de Laravel)',
    'cron_option_c_tag'                => 'Recomendado para VPS',
    'cron_option_c_desc_html'          => 'Conéctese por SSH como su usuario web y ejecute <code class="ss-chip-light">crontab -e</code>, luego añada:',
    'cron_option_c_hint_html'          => 'Esto evita <code class="ss-chip-light">cron.php</code> y utiliza el programador nativo de Laravel — menor sobrecarga, mejor para VPS o servidores dedicados con colas Redis.',

    // ─── Botón de copia de Cron + verificación ───
    'cron_copy'                        => 'Copiar',
    'cron_copied'                      => 'Copiado',
    'cron_verify_text_html'            => '<strong>Verifique que funcione:</strong> espere 2 minutos después de guardar su cron, luego revise la página "Cola y trabajadores" (dentro de cualquier espacio de trabajo de tenant) — si los trabajos están fluyendo, la conexión mostrará una marca de tiempo reciente. También puede ejecutar <code class="ss-chip-success">php artisan schedule:list</code> desde SSH para ver cada tarea registrada.',
];
