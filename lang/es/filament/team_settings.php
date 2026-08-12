<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| TeamSettingsPage — Filament tenant strings
|------------------------------------------------------------
*/

return [
    'title'                          => 'Gestión del Equipo y plazas',
    'navigation_label'               => 'Equipo y plazas',

    // Notifications - member ops
    'member_removed'                 => ':name eliminado de este espacio de trabajo.',
    'member_suspended_title'         => ':name suspendido.',
    'member_suspended_body'          => 'Ya no podrá iniciar sesión.',
    'member_reactivated'             => ':name reactivado.',

    // Reset password
    'reset_link_sent_title'          => 'Enlace de restablecimiento enviado',
    'reset_link_sent_body'           => ':email recibirá un correo en un minuto. Enlace válido durante 60 minutos.',
    'reset_link_failed_title'        => 'No se pudo enviar el enlace de restablecimiento',

    // Invite action
    'action_invite'                  => 'Invitar miembro del Equipo',
    'invite_email_label'             => 'Correo',
    'invite_email_placeholder'       => 'companero@empresa.com',
    'invite_role_label'              => 'Rol',
    'invite_role_manager'            => 'Gestor',
    'invite_role_member'             => 'Miembro',
    'invite_failed_title'            => 'No se pudo enviar la invitación',
    'invite_sent_title'              => 'Invitación enviada',
    'invite_sent_body'               => 'Enviado por correo a :email.',

    // Adjust seats action
    'action_adjust_seats'            => 'Ajustar límite de plazas',
    'max_seats_label'                => 'Plazas máximas',

    // ─── Blade view — role permissions banner ─────────────────────────
    'role_permissions_title'         => 'Permisos de rol',
    'role_permissions_lede'          => 'Dos niveles para los miembros del Equipo. Ambos están limitados al inquilino: nunca ven datos fuera de este espacio de trabajo.',
    'role_card_manager_title'        => 'Gestor',
    'role_card_manager_desc'         => 'Acceso completo a clientes potenciales, embudos, automatizaciones y formularios. Puede invitar y suspender al Equipo. No puede eliminar administradores.',
    'role_card_member_title'         => 'Miembro',
    'role_card_member_desc'          => 'Usuario estándar. Trabaja con clientes potenciales, consulta formularios e informes. Sin gestión del Equipo ni acceso a configuración.',

    // ─── Blade view — seat usage card ─────────────────────────────────
    'seat_usage_title'               => 'Uso de plazas',
    'seat_usage_subtitle'            => ':used de :max plazas en uso',
    'seat_count_suffix'              => 'disponibles',
    'seat_plan_prefix'               => 'Plan:',
    'seat_limit_msg'                 => 'Límite de plazas alcanzado. Suspenda o elimine un miembro, o mejore su Plan, para invitar a nuevos Usuarios.',

    // ─── Blade view — members table ───────────────────────────────────
    'members_title'                  => 'Miembros del Equipo',
    'col_name'                       => 'Nombre',
    'col_email'                      => 'Correo',
    'col_role'                       => 'Rol',
    'col_status'                     => 'Estado',
    'col_actions'                    => 'Acciones',
    'row_self_suffix'                => '(usted)',
    'status_suspended'               => 'Suspendido',
    'status_active'                  => 'Activo',
    'action_reset_password'          => 'Restablecer contraseña',
    'action_reset_password_title'    => 'Enviar correo de restablecimiento de contraseña',
    'action_unsuspend'               => 'Reactivar',
    'action_suspend'                 => 'Suspender',
    'action_remove'                  => 'Eliminar',
    'empty_no_members_html'          => 'Aún no hay miembros del Equipo.  Use la acción <strong>Invitar miembro del Equipo</strong> de arriba para empezar.',

    // ─── Blade view — wire:confirm prompts ────────────────────────────
    'confirm_reset_password'         => '¿Enviar un enlace de restablecimiento de contraseña a :email?',
    'confirm_suspend'                => '¿Suspender a :name?  Perderá el acceso hasta que se le reactive.',
    'confirm_remove'                 => '¿Eliminar a :name de este espacio de trabajo?',

    // ─── Role badges (Pass 22) ────────────────────────────────────────
    'role_admin'                     => 'Administrador',
    'role_owner'                     => 'Propietario',
    'role_manager'                   => 'Gestor',
    'role_member'                    => 'Miembro',
    'role_user'                      => 'Usuario',

    // ─── Role change (inline editor) ──────────────────────────────────
    'change_role_label'              => 'Cambiar rol',
    'role_changed'                   => 'El rol de :name se cambió a :role.',
    'role_invalid'                   => 'Rol no válido.',
    'role_cannot_change_self'        => 'No puede cambiar su propio rol.',
    'role_admin_only'                => 'Solo un administrador puede cambiar el rol de un administrador.',
    'role_admin_not_editable'        => 'Los roles de administrador los gestiona el propietario del espacio de trabajo, no desde aquí.',

    // ─── Seat-card plan labels (Pass 22) ──────────────────────────────
    'plan_free'                      => 'Gratuito',
    'plan_starter'                   => 'Inicio',
    'plan_pro'                       => 'Pro',
    'plan_business'                  => 'Empresa',
    'plan_enterprise'                => 'Enterprise',
    'plan_trial'                     => 'Prueba',
];
