<?php

declare(strict_types=1);

return [
    'subject'    => '[:count] new leads in ":name"',
    'body_intro' => 'Your saved filter ":name" has :count new matching lead(s):',
    'body_lead'  => '- :full_name (:email) [:status]',
    'body_view'  => 'View them: :url',
];
