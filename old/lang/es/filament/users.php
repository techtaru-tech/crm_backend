<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — UserResource translation strings
|--------------------------------------------------------------------------
|
| Labels, action copy, modal text and notifications for the Team Members
| (Users) resource at /admin/users.
| Consumed via __('filament/users.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'Miembros del equipo',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'Miembro del equipo',
    'plural_model_label'                => 'Miembros del equipo',

    // ─── Form fields ───────────────────────────────────────────────────
    'name'                              => 'Nombre',
    'email'                             => 'Correo electrónico',
    'password'                          => 'Contraseña',
    'avatar'                            => 'Avatar',
    'created_at'                        => 'Creado',

    // ─── Form ──────────────────────────────────────────────────────────
    'password_helper'                   => 'Déjelo en blanco para mantener la contraseña actual.',
    'role'                              => 'Rol',
    'two_factor_enabled'                => '2FA habilitado',

    // ─── Table columns ─────────────────────────────────────────────────
    'two_factor_short'                  => '2FA',
    'status'                            => 'Estado',
    'status_suspended'                  => 'Suspendido',
    'status_active'                     => 'Activo',

    // ─── Meeting link action ───────────────────────────────────────────
    'action_meeting_link'               => 'Enlace de reunión',
    'booking_links_suffix'              => ' — enlaces de reserva',
    'modal_close'                       => 'Cerrar',

    // ─── Password reset action ─────────────────────────────────────────
    'action_send_password_reset'        => 'Enviar restablecimiento de contraseña',
    'reset_modal_heading_prefix'        => 'Enviar un enlace de restablecimiento a ',
    'reset_modal_heading_suffix'        => '?',
    'reset_modal_description'           => 'El usuario recibirá un correo con un enlace para elegir una nueva contraseña. El enlace caduca en 60 minutos.',
    'reset_sent_title'                  => 'Enlace de restablecimiento enviado',
    'reset_sent_body_prefix'            => 'Enviado a ',
    'reset_sent_body_suffix'            => ' — válido durante 60 minutos.',
    'reset_failed_title'                => 'No se pudo enviar el enlace de restablecimiento',

    // ─── Suspend action ────────────────────────────────────────────────
    'action_suspend'                    => 'Suspender',
    'suspend_modal_heading_prefix'      => 'Suspender ',
    'suspend_modal_heading_suffix'      => '?',
    'suspend_modal_description'         => 'Perderán el acceso de inmediato. Reactívelos en cualquier momento con la acción Quitar suspensión.',
    'suspend_notification_title'        => 'Usuario suspendido',
    'suspend_notification_body_suffix'  => ' ya no puede iniciar sesión.',

    // ─── Unsuspend action ──────────────────────────────────────────────
    'action_unsuspend'                  => 'Quitar suspensión',
    'unsuspend_notification_title'      => 'Usuario reactivado',
    'unsuspend_notification_body_suffix' => ' puede volver a iniciar sesión.',

    // ─── Invite team member ────────────────────────────────────────────
    'invite_action_label'               => 'Invitar miembro del equipo',
    'invite_email_label'                => 'Dirección de correo electrónico',
    'invite_failed_title'               => 'No se pudo enviar la invitación',
    'invite_sent_title'                 => 'Invitación enviada a :email',

    // ─── CreateUser notifications ──────────────────────────────────────
    'create_failed_title'               => 'No se pudo crear el usuario',
    'create_seat_limit_title'           => 'Límite de plazas alcanzado',
    'create_email_taken_title'          => 'Esa dirección de correo ya está en uso',

    // ─── CreateUser (invitation flow) ──────────────────────────────────
    'create_invite_title'               => 'Invitar miembro del equipo',
    'create_invite_heading'             => 'Invitar a un nuevo miembro del equipo',
    'create_invite_subheading'          => 'Recibirá un correo con un enlace para establecer su nombre y contraseña.',
    'create_no_workspace_title'         => 'Sin contexto de espacio de trabajo',
    'create_no_workspace_body'          => 'No pudimos identificar su espacio de trabajo. Cierre sesión y vuelva a entrar, luego inténtelo de nuevo.',
    'create_invite_sent_title'          => 'Invitación enviada',
    'create_invite_sent_body'           => 'Se ha enviado un correo con un enlace de configuración a :email.',

    // ─── Select options ────────────────────────────────────────────
    'option_role_manager'               => 'Gerente',
    'option_role_member'                => 'Miembro',

    // ─── Booking-links modal (per-user list) ───────────────────────
    'booking_links_minutes_suffix'      => 'min',

    // ─── ListUsers subheading (role-permissions banner) ────────────
    // Rendered via resources/views/filament/resources/users/list-subheading.blade.php
    // — see App\Filament\Resources\UserResource\Pages\ListUsers::getSubheading().
    'subheading_role_permissions_title' => 'Permisos de rol',
    'subheading_intro'                  => 'Dos niveles para miembros del equipo. Ambos están limitados al inquilino — nunca ven datos fuera de este espacio de trabajo.',
    'subheading_manager_title'          => 'Gerente',
    'subheading_manager_desc'           => 'Clientes potenciales + embudos + automatizaciones + formularios completos. Puede invitar y suspender al equipo. No puede eliminar administradores.',
    'subheading_member_title'           => 'Miembro',
    'subheading_member_desc'            => 'Usuario estándar. Trabaja con clientes potenciales, ve formularios e informes. Sin gestión de equipo ni configuración.',
];
