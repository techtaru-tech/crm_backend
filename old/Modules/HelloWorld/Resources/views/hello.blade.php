<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ __('helloworld::hello_world.page_title') }}</title>
    {{-- The inline <style> block here is intentional and lives with this
         scaffold module so the HelloWorld example renders self-contained
         even when no asset pipeline is configured. Buyer-modules can
         either keep this pattern or extract per-view stylesheets — both
         are documented in the module SDK guide. --}}
    <style>
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f9fafb;color:#111827;margin:0;padding:80px 24px;text-align:center}
        .card{max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:48px 40px;box-shadow:0 10px 30px rgba(0,0,0,.04)}
        h1{font-size:28px;margin:0 0 12px}
        p{color:#6b7280;font-size:15px;line-height:1.6;margin:0 0 16px}
        code{background:#f3f4f6;padding:2px 6px;border-radius:4px;font-family:ui-monospace,monospace;font-size:13px}
    </style>
</head>
<body>
    <div class="card">
        <h1>👋 {{ __('helloworld::hello_world.greeting', ['name' => $name ?? config('leadhub.branding.app_name', 'LeadHub')]) }}</h1>
        <p>{!! __('helloworld::hello_world.proof_paragraph', ['path' => '<code>Modules/HelloWorld</code>']) !!}</p>
        <p>{{ __('helloworld::hello_world.starting_point_paragraph') }}</p>
    </div>
</body>
</html>
