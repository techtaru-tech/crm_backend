<?php

declare(strict_types=1);

return [

    // ----- Page title / navigation -----
    'title'                       => 'Protección reCAPTCHA',
    'navigation_label'            => 'reCAPTCHA',

    // ----- Google reCAPTCHA v3 section -----
    'console_intro_prefix'        => 'Obtenga sus claves desde',
    'console_link_label'          => 'Consola de administración de Google reCAPTCHA',
    'console_intro_suffix'        => 'Elija v3 al crear el sitio. Añada :host a la lista de dominios.',
    'master_switch_label'         => 'Interruptor maestro',
    'master_switch_helper'        => 'Active o desactive reCAPTCHA sin perder sus claves. Cuando está desactivado, todas las protecciones siguientes se ignoran.',
    'site_key_label'              => 'Clave del sitio',
    'site_key_helper'             => 'Clave pública. Se envía al navegador.',
    'secret_key_label'            => 'Clave secreta',
    'secret_key_helper'           => 'Clave solo de servidor. Se usa para verificar los tokens enviados contra Google.',
    'min_score_label'             => 'Puntuación mínima',
    'min_score_helper'            => 'Puntuación entre 0,0 (bot seguro) y 1,0 (humano seguro). Los envíos por debajo de este valor se rechazan. Google recomienda 0,5.',

    // ----- Protected surfaces section -----
    'protected_surfaces_description' => 'Interruptores por página. Desactive individualmente si ve rechazos de falsos positivos; el interruptor maestro de arriba prevalece sobre estos.',
    'guard_register_label'        => 'Formulario público /register',
    'guard_register_helper'       => 'Protege el formulario de autoregistro de espacios de trabajo.',
    'guard_admin_login_label'     => 'Inicio de sesión del inquilino /admin',
    'guard_admin_login_helper'    => 'Protege la página de inicio de sesión del inquilino.',
    'guard_sa_login_label'        => 'Inicio de sesión de superadministrador',
    'guard_sa_login_helper'       => 'Protege la página de inicio de sesión del superadministrador.',

    // ----- Notifications & actions -----
    'settings_saved'              => 'Configuración de reCAPTCHA guardada',
    'action_save'                 => 'Guardar',

];
