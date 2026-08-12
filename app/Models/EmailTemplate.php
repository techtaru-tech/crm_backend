<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class EmailTemplate extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'subject', 'body_html', 'body_text',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function render(Lead $lead): array
    {
        $vars = [
            '{{lead.first_name}}'  => $lead->first_name ?? '',
            '{{lead.last_name}}'   => $lead->last_name ?? '',
            '{{lead.email}}'       => $lead->email ?? '',
            '{{lead.phone}}'       => $lead->phone ?? '',
            '{{lead.company}}'     => $lead->company ?? '',
            '{{lead.status}}'      => $lead->status?->value ?? '',
            '{{lead.source}}'      => $lead->source ?? '',
        ];

        return [
            'subject'   => strtr($this->subject, $vars),
            'body_html' => strtr($this->body_html, $vars),
            'body_text' => strtr($this->body_text ?? strip_tags($this->body_html), $vars),
        ];
    }
}
