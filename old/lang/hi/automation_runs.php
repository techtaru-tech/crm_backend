<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Automation run log + action defaults
|------------------------------------------------------------
*/

return [

    'log' => [
        'condition_not_met'   => 'शर्त पूरी नहीं हुई — स्वचालन रोका गया।',
        'delaying_minutes'    => ':minutes मिनट विलंबित किया जा रहा है',
        'email_send_failed'   => 'ईमेल भेजने में विफल: ',
        'unknown_error'       => 'अज्ञात त्रुटि',
    ],

    'defaults' => [
        'task_title'              => 'फॉलो-अप कार्य',
        'notify_users_message'    => 'लीड पर स्वचालन ट्रिगर हुआ: :lead',
        'slack_message'           => 'लीड के लिए LeadHub स्वचालन ट्रिगर हुआ: :lead',
    ],

    'status' => [
        'success' => 'सफल',
        'failed'  => 'विफल',
        'running' => 'चल रहा है',
        'pending' => 'लंबित',
        'partial' => 'आंशिक',
        'passed'  => 'उत्तीर्ण',
        'skipped' => 'छोड़ा गया',
        'ok'      => 'ठीक',
    ],

];
