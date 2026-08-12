<?php

declare(strict_types=1);

return [

    // --- Navigation ----------------------------------------------------
    'nav_label'  => 'Marca',

    // --- Page heading ---------------------------------------------------
    'page_title' => 'Marca y marca blanca',

    // --- Section descriptions ------------------------------------------
    'identity_description'       => 'Nombre de la aplicación y logotipo — reemplaza "LeadHub" en todas partes.',
    'colors_description'         => 'Las propiedades personalizadas de CSS se regeneran al guardar y se inyectan en todas las vistas con alcance del inquilino.',
    'login_page_description'     => 'Personalice la apariencia de la página de inicio de sesión para sus usuarios.',
    'email_branding_description' => 'Estos valores aparecen en todos los correos electrónicos salientes enviados por la plataforma.',

    // --- Identity section ----------------------------------------------
    'application_name'        => 'Nombre de la aplicación',
    'upload_logo'             => 'Subir logotipo (PNG/SVG, recomendado 300×80 px)',
    'upload_logo_helper'      => 'La carga reemplaza la URL siguiente. Se muestra en la barra superior, página de inicio de sesión, correos electrónicos y PDF.',
    'logo_url'                => 'O URL del logotipo',
    'logo_url_placeholder'    => 'https://cdn.suempresa.com/logo.png',
    'logo_url_helper'         => 'Proporcione una URL si no carga un archivo. Deje en blanco para usar el archivo cargado.',
    'upload_favicon'          => 'Subir favicon (ICO/PNG, 32×32 px)',
    'upload_favicon_helper'   => 'La carga reemplaza la URL siguiente.',
    'favicon_url'             => 'O URL del favicon',
    'favicon_url_placeholder' => 'https://cdn.suempresa.com/favicon.ico',
    'favicon_url_helper'      => 'Proporcione una URL si no carga un archivo.',

    // --- Colors section ------------------------------------------------
    'primary_color' => 'Color primario',
    'accent_color'  => 'Color de acento',

    // --- Login page section --------------------------------------------
    'background_color'         => 'Color de fondo',
    'background_color_helper'  => 'Se usa cuando no hay imagen de fondo configurada.',
    'upload_login_bg'          => 'Subir imagen de fondo de inicio de sesión',
    'upload_login_bg_helper'   => 'Recomendado: 1920×1080 px. La carga reemplaza la URL siguiente.',
    'login_bg_url'             => 'O URL de imagen de fondo',
    'login_bg_url_placeholder' => 'https://cdn.suempresa.com/login-bg.jpg',

    // --- Email branding section ----------------------------------------
    'email_sender_name'       => 'Nombre del remitente del correo',
    'email_from'              => 'Dirección de correo del remitente',
    'footer_text'             => 'Texto de pie de página de correos y formularios',
    'footer_text_placeholder' => '© 2026 SuEmpresa. Todos los derechos reservados.',
    'footer_text_helper'      => 'Se muestra al final de todos los correos salientes y formularios públicos.',

    // --- Header actions ------------------------------------------------
    'save_branding' => 'Guardar marca',

];
