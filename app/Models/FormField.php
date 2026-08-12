<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    protected $fillable = [
        'form_id', 'type', 'label', 'placeholder', 'required',
        'field_key', 'options', 'sort_order', 'step_number', 'locked',
    ];

    protected $casts = [
        'required' => 'boolean',
        'locked'   => 'boolean',
        'options'  => 'array',
    ];

    public const TYPES = [
        'text'           => 'Short Text',
        'textarea'       => 'Long Text',
        'email'          => 'Email',
        'phone'          => 'Phone',
        'number'         => 'Number',
        'select'         => 'Dropdown',
        'checkbox'       => 'Checkbox',
        'multi_checkbox' => 'Multi-Checkbox',
        'radio'          => 'Radio Buttons',
        'file'           => 'File Upload',
        'date'           => 'Date Picker',
        'hidden'         => 'Hidden Field',
        'gdpr'           => 'GDPR Consent',
        'divider'        => 'Section Divider',
    ];

    /**
     * Translated field-type labels for Filament Select / display.
     * Keys mirror self::TYPES.
     */
    public static function typeLabels(): array
    {
        return [
            'text'           => __('models/form_field.type_text'),
            'textarea'       => __('models/form_field.type_textarea'),
            'email'          => __('models/form_field.type_email'),
            'phone'          => __('models/form_field.type_phone'),
            'number'         => __('models/form_field.type_number'),
            'select'         => __('models/form_field.type_select'),
            'checkbox'       => __('models/form_field.type_checkbox'),
            'multi_checkbox' => __('models/form_field.type_multi_checkbox'),
            'radio'          => __('models/form_field.type_radio'),
            'file'           => __('models/form_field.type_file'),
            'date'           => __('models/form_field.type_date'),
            'hidden'         => __('models/form_field.type_hidden'),
            'gdpr'           => __('models/form_field.type_gdpr'),
            'divider'        => __('models/form_field.type_divider'),
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function submissionValues(): HasMany
    {
        return $this->hasMany(FormSubmissionValue::class);
    }
}
