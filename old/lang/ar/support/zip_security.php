<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ZipSecurity — zip-slip guard exception messages
|--------------------------------------------------------------------------
|
| Translation strings used by ZipSecurity when an archive entry fails
| the path-traversal check. Accessed via __('support/zip_security.<key>').
|
*/

return [
    'unsafe_path' => 'تم اكتشاف مسار غير آمن في الأرشيف: :entry',
];
