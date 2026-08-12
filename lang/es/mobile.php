<?php

declare(strict_types=1);

return [

    // ─── Títulos de página ────────────────────────────────────────
    'page_title_scan'           => 'Escanear una tarjeta',
    'page_title_capture'        => 'Capturar lead',
    'page_title_offline'        => 'Sin conexión',
    'page_title_mobile'         => 'Móvil',

    // ─── Layout ───────────────────────────────────────────────────
    'admin_link'                => 'Administración',
    'install_button'            => 'Instalar',
    'offline_banner'            => 'Está sin conexión. Las capturas se pondrán en cola y se sincronizarán cuando vuelva a tener conexión.',
    'install_prompt_prefix'     => 'Instalar',
    'install_prompt_suffix'     => 'en su pantalla de inicio para captura sin conexión.',

    // ─── Página de escaneo ────────────────────────────────────────
    'scan_heading'              => 'Escanear una tarjeta de visita',
    'read_the_card'             => 'Leer la tarjeta',
    'review_and_save'           => 'Revisar y guardar',
    'save_lead'                 => 'Guardar lead',
    'scan_hint'                 => 'Haga una foto de la parte frontal de la tarjeta. La leeremos y rellenaremos previamente el formulario. Requiere conexión a Internet.',
    'open_camera_gallery'       => 'Abrir cámara / galería',
    'file_size_hint'            => 'JPG o PNG, hasta 8 MB',

    // ─── Etiquetas de los campos del formulario de lead ───────────
    'first_name'                => 'Nombre',
    'last_name'                 => 'Apellidos',
    'email'                     => 'Correo electrónico',
    'phone'                     => 'Teléfono',
    'company'                   => 'Empresa',
    'notes'                     => 'Notas',
    'assign_to'                 => 'Asignar a',
    'title'                     => 'Cargo',
    'website'                   => 'Sitio web',
    'notes_placeholder'         => '¿De qué hablaron?',
    'assign_to_me'              => 'Yo (:name)',

    // ─── Página de captura ────────────────────────────────────────
    'new_lead_heading'          => 'Nuevo lead',
    'scan_a_card_instead'       => 'Escanear una tarjeta en su lugar',
    'capture_hint'              => 'Lo capturado aquí aparece al instante en su espacio de trabajo — o se pone en cola y se sincroniza si está sin conexión.',

    // ─── Página sin conexión ──────────────────────────────────────
    'youre_offline'             => 'Está sin conexión',
    'capture_a_lead_anyway'     => 'Capturar un lead de todos modos',
    'offline_hint'              => 'Pero no se preocupe — LeadHub guarda las capturas localmente y las sincroniza en cuanto vuelva a tener conexión.',

    // ─── Cadenas JS de scan.blade.php ─────────────────────────────
    'js_reading'                => 'Leyendo…',
    'js_ocr_failed'             => 'OCR falló',
    'js_ocr_unavailable'        => 'OCR no disponible. Escríbalo manualmente.',
    'js_save_failed'            => 'Error al guardar',

    // ─── Cadenas JS de capture.blade.php ──────────────────────────
    'js_add_email_or_phone'     => 'Añada primero un correo electrónico o un teléfono.',
    'js_saving'                 => 'Guardando…',
    'js_saved'                  => 'Guardado.',
    'js_offline_queued'         => 'Sin conexión — guardado en la cola. Se sincronizará cuando vuelva a tener conexión.',

    // ─── Mensajes JSON del controlador móvil ──────────────────────
    'capture_error_email_or_phone_required'   => 'Se requiere un correo electrónico o un teléfono.',
    'capture_dedup_existing_lead'             => 'Se encontró un lead coincidente — mostrando el registro existente.',
    'capture_success_lead_captured'           => 'Lead capturado.',
    'ocr_error_openai_key_missing'            => 'La clave de la API de OpenAI no está configurada en el servidor.',
    'ocr_error_request_failed'                => 'La solicitud OCR falló.',
    'ocr_error_provider_status'               => 'El proveedor OCR devolvió :status',
    'ocr_error_parse_failed'                  => 'No se pudo analizar la respuesta OCR.',

    'ocr_business_card_prompt' => 'Extrae los campos de la tarjeta de visita de esta foto. Responde solo en el idioma con el código de configuración regional ":locale". Responde ÚNICAMENTE con un objeto JSON con las claves: first_name, last_name, email, phone, company, title, website. Usa null para cualquier campo que no puedas leer con seguridad. No inventes datos.',
];
