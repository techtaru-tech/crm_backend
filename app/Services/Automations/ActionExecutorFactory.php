<?php

namespace App\Services\Automations;

use App\Models\AutomationRun;
use App\Models\Lead;
use App\Services\Automations\Actions\AssignLeadAction;
use App\Services\Automations\Actions\ChangeStatusAction;
use App\Services\Automations\Actions\CreateTaskAction;
use App\Services\Automations\Actions\EnrollAiSdrAction;
use App\Services\Automations\Actions\MovePipelineAction;
use App\Services\Automations\Actions\NotifyUsersAction;
use App\Services\Automations\Actions\SendEmailAction;
use App\Services\Automations\Actions\SendSlackAction;
use App\Services\Automations\Actions\SendSmsAction;
use App\Services\Automations\Actions\SendWebhookAction;
use App\Services\Automations\Actions\SendWhatsAppAction;
use App\Services\Automations\Actions\TagAction;

class ActionExecutorFactory
{
    public function execute(string $actionType, Lead $lead, array $config, AutomationRun $run): bool
    {
        return match ($actionType) {
            'send_email'    => app(SendEmailAction::class)->execute($lead, $config, $run),
            'notify_users'  => app(NotifyUsersAction::class)->execute($lead, $config, $run),
            'assign_lead'   => app(AssignLeadAction::class)->execute($lead, $config, $run),
            'add_tag'       => app(TagAction::class)->execute($lead, $config, $run, true),
            'remove_tag'    => app(TagAction::class)->execute($lead, $config, $run, false),
            'move_pipeline' => app(MovePipelineAction::class)->execute($lead, $config, $run),
            'change_status' => app(ChangeStatusAction::class)->execute($lead, $config, $run),
            'send_webhook'  => app(SendWebhookAction::class)->execute($lead, $config, $run),
            'create_task'   => app(CreateTaskAction::class)->execute($lead, $config, $run),
            'send_slack'    => app(SendSlackAction::class)->execute($lead, $config, $run),
            'send_sms'      => app(SendSmsAction::class)->execute($lead, $config, $run),
            'send_whatsapp' => app(SendWhatsAppAction::class)->execute($lead, $config, $run),
            'enroll_ai_sdr' => app(EnrollAiSdrAction::class)->execute($lead, $config, $run),
            default         => false,
        };
    }
}
