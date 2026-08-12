/*!
 |------------------------------------------------------------------
 | lead-volume-report.js — Leads-over-time Chart.js initialiser
 |------------------------------------------------------------------
 |
 | Paired with resources/views/filament/pages/reports/lead-volume-report.blade.php.
 |
 | The previous revision built the chart from an inline Alpine.js
 | x-data init() that relied on a `defer`ed Chart.js loaded by the
 | panel render hook. On Livewire SPA navigation + filter morphs the
 | timing of "defer-script loaded vs Alpine init() running" wasn't
 | reliable — init() would call `new Chart()` before window.Chart was
 | defined, throw silently inside Alpine, and the canvas stayed blank.
 |
 | This file is loaded synchronously by an @push('scripts') block in
 | the view, so by the time it runs Chart.js has already been parsed
 | and the global is available. Filter changes (wire:model.live) emit
 | livewire:morph(ed) events; we listen for those and re-init the
 | chart against the freshly-rendered JSON island.
 |
 | Dynamic per-tenant data (labels + counts + translated dataset
 | label) is exposed as a JSON island the view writes server-side:
 |     <script type="application/json" id="lead-volume-chart-data">{...}</script>
 | so this file contains zero Blade syntax and is served statically.
 */
(function () {
    'use strict';

    var CANVAS_ID = 'lead-volume-chart';
    var ISLAND_ID = 'lead-volume-chart-data';

    function initLeadVolumeChart() {
        if (typeof window.Chart === 'undefined') {
            return;
        }
        var canvas = document.getElementById(CANVAS_ID);
        if (!canvas) {
            return;
        }

        // Destroy any prior Chart instance attached to this canvas. With
        // wire:key on the outer wrapper Livewire replaces the canvas
        // element on filter changes, but if Livewire morph reuses the
        // node we'd otherwise hit "Canvas is already in use".
        var existing = window.Chart.getChart(canvas);
        if (existing) {
            existing.destroy();
        }

        var island = document.getElementById(ISLAND_ID);
        if (!island) {
            return;
        }
        var payload;
        try {
            payload = JSON.parse(island.textContent || '{}');
        } catch (e) {
            return;
        }

        var labels = Array.isArray(payload.labels) ? payload.labels : [];
        var data = Array.isArray(payload.data) ? payload.data : [];
        var chartLabel = typeof payload.chartLabel === 'string' && payload.chartLabel.length
            ? payload.chartLabel
            : 'Leads';

        new window.Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: chartLabel,
                    data: data,
                    borderColor: 'rgba(99, 102, 241, 1)',
                    backgroundColor: 'rgba(99, 102, 241, 0.15)',
                    tension: 0.3,
                    fill: true,
                    // Hide points when the series gets dense — matches
                    // the threshold from the previous Alpine init.
                    pointRadius: labels.length > 30 ? 0 : 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Initial page load (full page render or hard refresh).
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLeadVolumeChart);
    } else {
        initLeadVolumeChart();
    }

    // Filament SPA-style navigation (wire:navigate). Fires after the
    // new page's DOM is in place.
    document.addEventListener('livewire:navigated', initLeadVolumeChart);

    // Filter changes (wire:model.live on the source/groupBy/dateRange
    // inputs) trigger Livewire morph. The 'livewire:morphed' event
    // fires after morph completes; the wire:key on the chart wrapper
    // means a new canvas element is in place by then.
    document.addEventListener('livewire:morphed', initLeadVolumeChart);
})();
