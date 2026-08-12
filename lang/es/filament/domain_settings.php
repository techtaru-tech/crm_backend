<?php

declare(strict_types=1);

return [

    // --- Navegación ----------------------------------------------------
    'nav_label'  => 'Dominio personalizado',

    // --- Título de la página -------------------------------------------
    'page_title' => 'Dominio personalizado',

    // --- Descripción de sección -------------------------------------------
    'section_description' => 'Apunte su dominio personalizado a esta plataforma. Añada un registro CNAME apuntando a: :host',

    // --- Etiquetas de campo --------------------------------------------
    'custom_domain'             => 'Dominio personalizado',
    'custom_domain_placeholder' => 'leads.suempresa.com',
    'custom_domain_helper'      => 'Introduzca su dominio sin https://. Luego añada un registro CNAME: leads.suempresa.com → :host',

    // --- Acciones de cabecera ------------------------------------------
    'reverify_dns' => 'Volver a verificar DNS',
    'save_domain'  => 'Guardar dominio',

    // ─── Vista Blade — instrucciones DNS ────────────────────────────────
    'current_domain_label'      => 'Dominio actual:',
    'status_verified'           => 'Verificado',
    'status_pending'            => 'Verificación pendiente',
    'dns_record_lede'           => 'Añada el siguiente registro DNS a su dominio:',
    'col_type'                  => 'Tipo',
    'col_name'                  => 'Nombre',
    'col_value'                 => 'Valor',
    'dns_txt_hint_html'         => 'O añada un registro TXT: <code class="ds-code">_leadhub-verify.:domain</code> → <code class="ds-code">:token</code>',
    'dns_propagation_hint'      => 'La propagación DNS puede tardar hasta 24 horas. La verificación se ejecuta automáticamente en segundo plano.',
    'dns_record_type_cname'     => 'CNAME',

];
