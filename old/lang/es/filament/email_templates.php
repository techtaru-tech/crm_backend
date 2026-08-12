<?php

declare(strict_types=1);

return [

    // ─── Navegación ───────────────────────────────────────────────────
    'nav_label'                         => 'Plantillas de correo',
    'model_label'                       => 'Plantilla de correo',
    'plural_model_label'                => 'Plantillas de correo',

    // ─── Formulario ────────────────────────────────────────────────────
    'template_name'                     => 'Nombre de la plantilla',
    'email_subject'                     => 'Asunto del correo',
    'subject_helper'                    => 'Admite: {{lead.first_name}}, {{lead.last_name}}, {{lead.email}}, {{lead.company}}, {{lead.source}}, {{lead.status}}',
    'html_body'                         => 'Cuerpo HTML',
    'html_body_helper'                  => 'Use {{lead.first_name}}, {{lead.email}}, etc. para personalización.',
    'plain_text_body'                   => 'Cuerpo en texto plano (opcional — se genera automáticamente si está vacío)',

    // ─── Columnas de tabla ─────────────────────────────────────────────
    'name'                              => 'Nombre',
    'subject'                           => 'Asunto',
    'created'                           => 'Creada',
];
