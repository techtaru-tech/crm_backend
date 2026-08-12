<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Flips the visitor's session language. Only accepts codes listed
 * in config('locales.supported') so a hostile link can't trigger
 * a missing-file error.
 */
class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = array_keys(config('locales.supported', ['en' => []]));

        if (in_array($locale, $supported, true)) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
