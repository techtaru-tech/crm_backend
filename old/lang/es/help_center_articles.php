<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Help Center FAQ articles — title + body translation strings
|--------------------------------------------------------------------------
|
| The 18 canonical FAQ articles rendered at /admin/help.  Buyers
| translate or adapt the copy by editing this file, or by copying
| it to lang/<locale>/help_center_articles.php and translating
| each value.
|
| Keys are stable slugs so translation files can stay in sync as
| article ordering changes.  Tags + categories use a sibling file
| structure for the same translation discipline.
|
| Tags are not translated by default — they're used as search-index
| keywords and filter chips, and operators frequently want them to
| match English for SEO / muscle memory.  Buyers who want translated
| tags can copy a tags slot into their target-locale file.
|
*/

return [

    // ─── Page chrome ────────────────────────────────────────────────
    'page_title'        => 'Ayuda y preguntas frecuentes',
    'nav_label'         => 'Ayuda y preguntas frecuentes',
    'heading'           => '¿En qué podemos ayudarle?',
    'subheading'        => 'Respuestas con búsqueda a las preguntas que todos los espacios de trabajo se hacen durante la primera semana. Si la suya no está aquí, contacte con soporte.',

    // ─── Categories (rendered as section headers) ───────────────────
    'category' => [
        'getting_started'   => 'Primeros pasos',
        'forms'             => 'Formularios',
        'pipelines'         => 'Embudos',
        'automations'       => 'Automatizaciones',
        'email'             => 'Correo electrónico',
        'billing'           => 'Facturación',
        'privacy'           => 'Privacidad y datos',
        'team'              => 'Equipo',
        'general'           => 'General',
    ],

    // ─── Articles ───────────────────────────────────────────────────
    // Each article: title + body (plain text, newlines preserved
    // by nl2br at the view layer).  Tags remain English by default.
    'articles' => [

        'add_first_lead' => [
            'title' => '¿Cómo añado mi primer cliente potencial?',
            'body'  => 'Vaya a Clientes potenciales en la barra lateral, haga clic en «Nuevo cliente potencial» y rellene al menos nombre + correo electrónico. El sistema crea automáticamente un enlace con la empresa si ya existe una empresa con ese dominio.',
        ],

        'import_csv' => [
            'title' => '¿Cómo importo clientes potenciales existentes desde una hoja de cálculo?',
            'body'  => 'Clientes potenciales → botón «Importar» (arriba a la derecha). Suba un CSV — el sistema asigna las columnas automáticamente. Campos comunes reconocidos: first_name, last_name, email, phone, company, source, status. Los campos personalizados pueden asignarse manualmente.',
        ],

        'branding_setup' => [
            'title' => '¿Dónde configuro los colores de marca y el logotipo?',
            'body'  => 'Configuración → Marca. Suba un logotipo (recomendamos PNG de 600x200 con fondo transparente) y establezca los colores primario y secundario. Los cambios se aplican a las páginas de inicio de sesión, correos electrónicos y páginas de aterrizaje en segundos.',
        ],

        'embed_form' => [
            'title' => '¿Cómo integro un formulario en mi sitio web?',
            'body'  => 'Formularios → haga clic en cualquier formulario → pestaña «Integrar». Copie el fragmento de iframe (funciona en cualquier sitio) o el fragmento de JavaScript (se carga en línea, mejor experiencia de usuario). Los envíos crean nuevos clientes potenciales automáticamente.',
        ],

        'prefill_form' => [
            'title' => '¿Puedo rellenar previamente los campos del formulario desde parámetros de URL?',
            'body'  => 'Sí. Añada ?email=foo@bar.com&first_name=Jane a la URL pública del formulario. Los campos coincidentes se rellenan previamente. Útil para campañas de correo electrónico personalizadas.',
        ],

        'pipeline_stages' => [
            'title' => '¿Cómo personalizo las etapas del embudo?',
            'body'  => 'Configuración → Embudos → seleccione un embudo → editar etapas. Arrastre para reordenar. Establezca el «% de probabilidad de ganar» para que la previsión ponderada del panel se ajuste a la realidad.',
        ],

        'pipeline_per_team' => [
            'title' => '¿Puedo tener embudos diferentes para equipos distintos?',
            'body'  => 'Sí — cada equipo puede tener su propio embudo. Marque como «predeterminado» el embudo en el que deberían recaer la mayoría de los clientes potenciales. Los miembros pueden cambiar modificando el pipeline_id del cliente potencial desde la vista de detalle.',
        ],

        'automation_intro' => [
            'title' => '¿Qué es una automatización?',
            'body'  => 'Un disparador + una lista de acciones que se ejecutan cuando se cumple la condición del disparador. Ejemplos: «Cuando se crea un nuevo cliente potencial → asignar por rotación → notificar a Slack → enviar correo de bienvenida». Constrúyalas en Automatizaciones → Nueva.',
        ],

        'automation_manual' => [
            'title' => '¿Cómo activo una automatización manualmente?',
            'body'  => 'Abra cualquier cliente potencial → acción «Ejecutar automatización» → elija de la lista. Útil para pruebas o para campañas puntuales en las que la condición del disparador no coincide perfectamente.',
        ],

        'smtp_connect' => [
            'title' => '¿Cómo conecto mi propio servidor SMTP?',
            'body'  => 'Configuración → Configuración de correo electrónico. Introduzca el host SMTP, el puerto, el usuario y la contraseña. Haga clic en «Enviar correo de prueba». Una vez verificado, todos los correos transaccionales (invitaciones, restablecimientos de contraseña, recordatorios de ciclo de vida) se enrutan a través de su servidor.',
        ],

        'email_deliverability' => [
            'title' => '¿Por qué no se entregan mis correos electrónicos?',
            'body'  => 'Tres cosas: (1) compruebe que Configuración → Configuración de correo electrónico → «Enviar correo de prueba» funciona, (2) verifique que su dominio tiene registros SPF + DKIM, (3) compruebe que el correo del cliente potencial es real (no comprobamos rebotes antes del envío). Si los tres puntos son correctos y los correos siguen rebotando, su IP de envío puede estar en una lista negra.',
        ],

        'payment_method' => [
            'title' => '¿Cómo actualizo mi método de pago?',
            'body'  => 'Cuenta → Facturación → «Gestionar suscripción» → «Actualizar método de pago». Será redirigido al portal seguro de su proveedor de pagos. Los datos de la tarjeta nunca pasan por nuestros servidores.',
        ],

        'download_invoices' => [
            'title' => '¿Cómo descargo mis facturas anteriores?',
            'body'  => 'Cuenta → Facturación. Las facturas anteriores aparecen bajo «Actividad reciente». Cada una se descarga como PDF.',
        ],

        'cancel_subscription' => [
            'title' => '¿Cómo cancelo mi suscripción?',
            'body'  => 'Cuenta → Facturación → «Cancelar suscripción». Mantendrá el acceso hasta el final del período de facturación actual. Sus datos se conservan durante 30 días después de la cancelación por si cambia de opinión.',
        ],

        'export_data' => [
            'title' => '¿Cómo exporto todos mis datos?',
            'body'  => 'Cuenta → Privacidad y datos → «Exportar mis datos». Se crea un ZIP en segundo plano que contiene todos los registros en formato JSON. El enlace de descarga es válido durante 48 horas.',
        ],

        'delete_workspace' => [
            'title' => '¿Cómo elimino mi espacio de trabajo de forma permanente?',
            'body'  => 'Cuenta → Privacidad y datos → «Eliminar mi espacio de trabajo». La eliminación se programa con 30 días de antelación — puede cancelarla en cualquier momento antes de esa fecha. Tras 30 días, todos los registros se eliminan de forma permanente.',
        ],

        'invite_member' => [
            'title' => '¿Cómo invito a un miembro del equipo?',
            'body'  => 'Configuración → Equipo y plazas → «Invitar miembro». Introduzca el correo electrónico + el rol (miembro o gerente). Recibirán un enlace de configuración válido durante 24 horas.',
        ],

        'member_vs_manager' => [
            'title' => '¿Cuál es la diferencia entre los roles de miembro y gerente?',
            'body'  => 'Miembro: puede trabajar con clientes potenciales (crear, editar, asignar), no puede cambiar la configuración ni invitar al equipo. Gerente: todo lo que puede hacer un miembro MÁS invitar/eliminar miembros y editar embudos. Administrador: propietario del espacio de trabajo — todos los permisos, además de poder eliminar el espacio de trabajo.',
        ],

    ],

];
