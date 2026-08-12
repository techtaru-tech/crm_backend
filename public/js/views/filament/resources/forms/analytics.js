/*!
 |------------------------------------------------------------------
 | analytics.js — Form-submission analytics Chart.js initialiser
 |------------------------------------------------------------------
 |
 | Paired with resources/views/filament/resources/forms/analytics.blade.php.
 | Replaces a previously-inline <script> @push('scripts') block to
 | satisfy CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | Dynamic per-tenant data (submissions-over-time series) is exposed
 | as a JSON island the view writes server-side:
 |     <script type="application/json" id="form-submissions-data">{...}</script>
 | so this file contains zero Blade syntax and is served statically.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('submissionsChart');
        if (!ctx || typeof Chart === 'undefined') {
            return;
        }

        var dataIsland = document.getElementById('form-submissions-data');
        if (!dataIsland) {
            return;
        }

        var payload;
        try {
            payload = JSON.parse(dataIsland.textContent || '{}');
        } catch (e) {
            return;
        }

        var labels = payload.labels || [];
        var data = payload.data || [];
        var chartLabel = ctx.dataset.chartLabel || 'Submissions';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: chartLabel,
                    data: data,
                    fill: true,
                    backgroundColor: 'rgba(99, 102, 241, 0.10)',
                    borderColor: '#6366f1',
                    tension: 0.3,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
})();
