/*!
 |------------------------------------------------------------------
 | api/docs.js — Swagger UI bootstrap for the API documentation page
 |------------------------------------------------------------------
 |
 | Paired with resources/views/api/docs.blade.php.
 | Replaces the previously-inline <script> block that interpolated
 | the OpenAPI spec via Blade.  The spec is now exposed as a JSON
 | island (a <script type="application/json" id="api-spec"> element
 | written server-side) so this file contains zero Blade syntax and
 | can be served as a static asset.
 */
(function () {
    'use strict';

    if (typeof SwaggerUIBundle === 'undefined') {
        return;
    }

    var island = document.getElementById('api-spec');
    if (!island) {
        return;
    }

    var spec;
    try {
        spec = JSON.parse(island.textContent || '{}');
    } catch (e) {
        // If the spec can't be parsed, render an empty Swagger UI
        // rather than crashing the page.
        spec = { openapi: '3.0.0', info: { title: 'Spec unavailable', version: '' }, paths: {} };
    }

    SwaggerUIBundle({
        spec: spec,
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
        layout: 'BaseLayout',
        persistAuthorization: true
    });
})();
