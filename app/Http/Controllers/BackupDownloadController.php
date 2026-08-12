<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Tiny dedicated controller so the Filament Backups page can link
 * to a plain HTTP download URL — far simpler than trying to stream
 * a multi-MB file through Livewire's action response.
 *
 * Gated by auth + SA check in the route definition.
 */
class BackupDownloadController extends Controller
{
    public function download(Request $request, BackupService $svc): BinaryFileResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $name = (string) $request->query('name', '');
        $path = $svc->path($name);

        abort_unless($path && file_exists($path), 404);

        return response()->download($path, $name);
    }
}
