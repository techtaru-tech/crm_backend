<?php

namespace App\Events;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Lead;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Form $form,
        public FormSubmission $submission,
        public ?Lead $lead,
    ) {}
}
