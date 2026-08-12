<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — cadenas de OnboardingWizard (es)
|--------------------------------------------------------------------------
|
| Etiquetas, descripciones, textos de ayuda y copias de notificaciones para
| el asistente de incorporación de primer uso en /admin/onboarding.
| Consumidas vía __('filament/onboarding_wizard.<clave>').
|
*/

return [

    // ─── Encabezado de la página ──────────────────────────────────────────────
    'title_prefix'                  => 'Bienvenido a :app',

    // ─── Aviso de slug del workspace ajustado ────────────────────────────────
    'slug_adjusted_title'           => 'Se ajustó el slug de su workspace',
    'workspace_renamed_body'        => 'El nombre «:requested» ya estaba ocupado, así que le hemos asignado «:assigned» en su lugar. Sus páginas públicas residirán bajo :url/{page-slug}.',

    // ─── Paso 1: Workspace ────────────────────────────────────────────────────
    'step_workspace'                => 'Workspace',
    'step_workspace_description'    => 'Dé nombre a su workspace y elija un color de marca',
    'workspace_name'                => 'Nombre del workspace',
    'workspace_name_helper'         => 'Aparece en la barra lateral y en los correos salientes.',
    'primary_color'                 => 'Color principal de marca',
    'primary_color_helper'          => 'Se usa para botones, enlaces y acentos en todo su workspace.',
    'company_tagline'               => 'Lema (opcional)',
    'company_tagline_helper'        => 'Se muestra en sus formularios públicos y firmas de correo.',

    // ─── Paso 2: Branding ─────────────────────────────────────────────────────
    'step_branding'                 => 'Branding',
    'step_branding_description'     => 'Suba el logotipo de su empresa',
    'company_logo'                  => 'Logotipo de la empresa',
    'company_logo_helper'           => 'PNG o SVG, menos de 2 MB. Omita este paso si prefiere añadirlo después.',

    // ─── Paso 3: Equipo ───────────────────────────────────────────────────────
    'step_team'                     => 'Equipo',
    'step_team_description'         => 'Invite a sus compañeros',
    'team_invitations'              => 'Miembros del equipo a invitar',
    'team_invitations_helper'       => 'Déjelo vacío si prefiere invitar a sus compañeros más tarde desde Configuración → Equipo.',
    'add_teammate'                  => 'Añadir compañero',
    'label_role'                    => 'Rol',

    // ─── Paso 4: Fuentes de clientes potenciales ──────────────────────────────
    'step_lead_sources'             => 'Fuentes de clientes potenciales',
    'step_lead_sources_description' => 'Nombre su primera fuente',
    'first_lead_source'             => 'Primera fuente',
    'first_lead_source_helper'      => 'Crearemos una fuente predeterminada para que pueda empezar a capturar clientes potenciales de inmediato.',
    'default_lead_source'           => 'Sitio web',

    // ─── Envío / Acciones de cabecera ────────────────────────────────────────
    'finish_setup'                  => 'Finalizar configuración',
    'skip_for_now'                  => 'Omitir por ahora',

    // ─── Notificación de error en invitación ─────────────────────────────────
    'invite_failed_prefix'          => 'No se pudo invitar a ',

    // ─── Notificación de éxito ───────────────────────────────────────────────
    'welcome_title'                 => '¡Bienvenido a bordo!',
    'welcome_body_invited'          => '{1} :count invitación enviada. Todo listo.|[2,*] :count invitaciones enviadas. Todo listo.',
    'welcome_body_no_invites'       => 'Todo listo. Busquemos su primer cliente potencial.',

    // ─── Vista Blade — bloque hero ───────────────────────────────────────────
    'hero_title'                    => 'Vamos a configurar su workspace',
    'hero_subtitle'                 => 'Esto lleva unos 2 minutos. Puede omitir cualquier paso y volver más tarde.',

    // ─── Opciones de selección ────────────────────────────────────────────
    'option_role_manager'           => 'Gestor',
    'option_role_member'            => 'Miembro',

    // ─── Etiquetas de campo ───────────────────────────────────────────────
    'field_email_label'             => 'Correo electrónico',

    // ─── Marcadores de campo ──────────────────────────────────────────────
    'placeholder_teammate_email'    => 'companero@empresa.com',
];
