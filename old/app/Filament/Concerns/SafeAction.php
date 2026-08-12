<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Helper for wrapping Filament Action callbacks so runtime
 * exceptions surface as a danger notification instead of
 * Filament's generic "Error while loading page" boundary.
 *
 * Usage inside an Action::action(...):
 *
 *     ->action(function (array $data) {
 *         static::safely('Action name for logs', function () use ($data) {
 *             // real work
 *         });
 *     })
 */
trait SafeAction
{
    /**
     * Run $callback; on any Throwable, log + show a danger
     * Filament Notification and return false.
     */
    protected static function safely(string $label, \Closure $callback): bool
    {
        try {
            $callback();
            return true;
        } catch (\Throwable $e) {
            Log::error("[Action] {$label} failed", [
                'error'     => $e->getMessage(),
                'exception' => $e::class,
                'trace'     => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title(__('notifications.something_went_wrong'))
                // Defense-in-depth: Filament's Notification::body()
                // explicitly allows "basic, safe HTML" per Filament docs —
                // it does NOT escape the value. Today all UI-facing
                // exceptions are thrown with translated messages, but a
                // future RuntimeException/Throwable that carries
                // user-controlled data (e.g., "Bad value: $userInput")
                // would render as HTML in the admin toast. e() escapes
                // the exception message so any future regression is inert.
                ->body(e($e->getMessage()))
                ->danger()
                ->send();

            return false;
        }
    }
}
