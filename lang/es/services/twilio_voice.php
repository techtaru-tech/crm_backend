<?php

declare(strict_types=1);

return [
    'missing_credentials' => 'Faltan credenciales de Twilio, teléfono de usuario o teléfono del lead.',
    'twilio_error'        => 'Error de Twilio: :body',
    'exception'           => 'Excepción: :error',

    'summary_system_prompt' => 'Eres un asistente de ventas. Responde únicamente en el idioma con el código de configuración regional «:locale». Resume las transcripciones de llamadas en exactamente 3 frases, seguidas de un único punto «:nextActionLabel:» que nombre el siguiente paso recomendado.',
    'summary_next_action_label' => 'Siguiente acción',
    'summary_transcript_label'  => 'Transcripción',
];
