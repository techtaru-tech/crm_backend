<?php

return [
    'nav_label' => 'Roles & Permissions',
    'title'     => 'Roles & Permissions',
    'save'      => 'Save permissions',
    'saved'     => 'Role permissions updated.',

    'role_manager'      => 'Manager',
    'role_manager_hint' => 'What managers can access. Managers always keep a baseline of view / create / edit / assign / export / import on records — here you control their delete, management and settings access.',

    'role_member'      => 'Member',
    'role_member_hint' => 'What standard members can access. Untick anything a member should not see or do — it takes effect on their next page load. (The Admin role always keeps full access and cannot be limited here.)',

    'role_viewer'      => 'Viewer',
    'role_viewer_hint' => 'What viewers can see. Viewers are read-only everywhere, so only the view permissions are listed here — tick the modules this role should be able to open.',
];
