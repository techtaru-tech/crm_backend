<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin UserResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_users.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_users.php.
*/

return [

    // ----- User details form -----
    'primary_workspace'           => 'Espacio de trabajo principal',
    'super_admin'                 => 'Super Admin',
    'super_admin_helper'          => 'Concede acceso al panel de Super Admin.',

    // ----- Table columns -----
    'workspace'                   => 'Espacio de trabajo',
    'sa'                          => 'SA',
    'status'                      => 'Estado',
    'status_active'               => 'Activo',
    'status_suspended'            => 'Suspendido',

    // ----- Filters -----
    'filter_role'                 => 'Rol',
    'filter_role_super_admin'     => 'Super Admin',
    'filter_role_regular_user'    => 'Usuario habitual',

    // ----- Reset password action -----
    'reset_password'              => 'Enviar enlace de restablecimiento',
    'reset_password_demo_guard'   => 'Demo: no se pueden restablecer contraseñas de super-admin.',
    'reset_password_modal_heading'=> '¿Enviar enlace de restablecimiento a :email?',
    'reset_password_modal_description' => 'Válido durante 60 minutos.',
    'reset_link_sent_title'       => 'Enlace de restablecimiento enviado',
    'reset_link_sent_body'        => 'Enviado a :email.',
    'reset_link_failed_title'     => 'No se pudo enviar el enlace de restablecimiento',
    'password_broker_rejected'    => 'El gestor de contraseñas lo rechazó (:status).',

    // ----- Suspend action -----
    'suspend'                     => 'Suspender',
    'suspend_demo_guard'          => 'Demo: no se pueden suspender usuarios.',
    'suspend_modal_heading'       => '¿Suspender a :name?',
    'suspend_modal_description'   => 'Perderán el acceso en todos los paneles hasta que se les retire la suspensión.',
    'user_suspended_title'        => 'Usuario suspendido',
    'user_suspended_body'         => ':email ya no puede iniciar sesión.',

    // ----- Unsuspend action -----
    'unsuspend'                   => 'Quitar suspensión',
    'unsuspend_demo_guard'        => 'Demo: no se puede quitar la suspensión a usuarios.',
    'user_reactivated_title'      => 'Usuario reactivado',
    'user_reactivated_body'       => ':email puede volver a iniciar sesión.',

    // ----- Field labels (form + table) -----
    'name'                        => 'Nombre',
    'email'                       => 'Correo electrónico',
    'created_at'                  => 'Creado el',

    // ----- Model labels -----
    'model_label'                 => 'Usuario',
    'plural_model_label'          => 'Usuarios',

];
