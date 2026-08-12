<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| Twilio Voice service — DB-persisted note strings
|------------------------------------------------------------
| Used by app/Services/Voice/TwilioVoiceService.php.
| Strings persist to lead_calls.notes and render in the lead
| call timeline. Translate at write-time against the
| dispatcher's current locale.
|
| Accessed via __('services/twilio_voice.<key>').
*/

return [
    'missing_credentials' => 'بيانات اعتماد Twilio أو هاتف المستخدم أو هاتف العميل المحتمل مفقودة.',
    'twilio_error'        => 'خطأ Twilio: :body',
    'exception'           => 'استثناء: :error',

    // Pass-33 i18n fix — OpenAI summarisation prompt routed through the
    // translator so the AI responds in the buyer's locale and labels the
    // next-action bullet with the locale-appropriate phrase.  Without
    // this, the AI defaulted to English for both the summary body and
    // the literal English "Next action:" prefix it was instructed to emit,
    // which then rendered verbatim inside the buyer's call timeline.
    // Buyer post-install: re-translate to keep the locale instruction
    // explicit (placeholder :locale is the Laravel app locale code).
    'summary_system_prompt' => 'أنت مساعد مبيعات. ردّ فقط باللغة ذات رمز اللغة ":locale". لخّص نصوص المكالمات في 3 جمل بالضبط، متبوعة بنقطة واحدة ":nextActionLabel:" تحدّد الخطوة التالية الموصى بها.',
    'summary_next_action_label' => 'الإجراء التالي',
    'summary_transcript_label'  => 'النص',
];
