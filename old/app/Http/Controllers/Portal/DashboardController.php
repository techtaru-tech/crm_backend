<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\LeadAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Lead-facing dashboard showing deal status, activity, attachments.
 *
 * Only surfaces data the lead is allowed to see — assigned rep NAME
 * only (no email), non-internal activities, NOT notes (internal).
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\Lead $lead */
        $lead = $request->attributes->get('portal_lead');
        $lead->loadMissing(['pipelineStage.pipeline', 'assignedUser', 'attachments.uploader', 'tags']);

        // Filter activity to types the lead should see (no internal notes).
        $publicActivityTypes = [
            'created', 'stage_moved', 'status_changed',
            'email_sent', 'email_received', 'meeting_booked',
            'call_logged', 'score_changed',
        ];

        $activities = $lead->activities()
            ->whereIn('type', $publicActivityTypes)
            ->latest('created_at')
            ->limit(30)
            ->get();

        $allStages = $lead->pipeline
            ? $lead->pipeline->stages()->orderBy('sort_order')->get()
            : collect();

        return view('portal.dashboard', [
            'lead'       => $lead,
            'tenant'     => $lead->tenant,
            'activities' => $activities,
            'allStages'  => $allStages,
        ]);
    }

    /**
     * Mime types accepted on the public portal. Deliberately narrow —
     * documents + images only, nothing executable by a web server.
     */
    protected const ALLOWED_MIMES = [
        'pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'csv', 'zip',
    ];

    public function uploadAttachment(Request $request)
    {
        /** @var \App\Models\Lead $lead */
        $lead = $request->attributes->get('portal_lead');

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:20480',                         // 20 MB
                'mimes:' . implode(',', self::ALLOWED_MIMES),
            ],
        ]);

        $file = $request->file('file');

        // NEVER trust the client's extension — extension() uses the
        // resolved MIME, getClientOriginalExtension() is user-controlled
        // and can be `.php` pointing at a PDF MIME.
        $safeExt   = strtolower((string) $file->extension());
        if (! in_array($safeExt, self::ALLOWED_MIMES, true)) {
            abort(422, __('messages.file_type_not_allowed'));
        }

        $filename  = Str::uuid() . '.' . $safeExt;
        $directory = "lead-attachments/{$lead->tenant_id}/{$lead->id}";
        $path      = $file->storeAs($directory, $filename, 'local');

        LeadAttachment::create([
            'tenant_id'     => $lead->tenant_id,
            'lead_id'       => $lead->id,
            'uploaded_by'   => null, // uploaded by the LEAD via portal
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'disk'          => 'local',
            'path'          => $path,
        ]);

        return redirect()->route('portal.dashboard')->with('success', __('messages.portal_file_uploaded'));
    }
}
