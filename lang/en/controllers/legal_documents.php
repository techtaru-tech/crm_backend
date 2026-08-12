<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LegalDocumentsController — DPA template fallbacks
|--------------------------------------------------------------------------
|
| Translation strings used by LegalDocumentsController. Accessed via
| __('controllers/legal_documents.<key>').
|
*/

return [
    'operator_fallback' => 'The Operator',
    // Filename shown in the browser save-as dialog when the DPA PDF
    // is streamed.  Buyers can localise by translating this key; the
    // .pdf extension is appended by the controller.
    'pdf_filename'      => 'Data-Processing-Agreement',
];
