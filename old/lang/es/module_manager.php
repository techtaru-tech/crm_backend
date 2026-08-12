<?php

declare(strict_types=1);

return [

    'error_system_unavailable'                  => 'Sistema de módulos no disponible: vuelve a subir el zip completo de la distribución de LeadHub y asegúrate de incluir la carpeta vendor/.',

    'error_upload_not_found'                    => 'Carga no encontrada: :path',
    'error_cannot_open_zip'                     => 'No se pudo abrir el archivo zip subido.',
    'error_missing_module_json'                 => 'El archivo subido no contiene un fichero module.json.',
    'error_module_json_missing_name'            => 'A module.json le falta el campo «name».',
    'error_invalid_module_name'                 => 'Nombre de módulo no válido en module.json.',
];
