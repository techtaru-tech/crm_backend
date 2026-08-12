<?php

namespace App\Services;

/**
 * Renders simple SVG bar/line charts for PDF export.
 * All chart data stays server-side — no external service calls.
 */
class SvgChartRenderer
{
    private const W = 900;
    private const H = 260;
    private const PAD_TOP = 20;
    private const PAD_BOTTOM = 50;
    private const PAD_LEFT = 55;
    private const PAD_RIGHT = 20;

    private const COLORS = [
        '#6366f1', '#10b981', '#f59e0b', '#ef4444',
        '#3b82f6', '#a855f7', '#ec4899', '#0ea5e9',
        '#eab308', '#84cc16', '#14b8a6', '#f97316',
    ];

    public function barChart(array $labels, array $values, string $label = 'Count'): string
    {
        if (empty($values) || max($values) === 0) {
            return $this->empty();
        }

        $chartW = self::W - self::PAD_LEFT - self::PAD_RIGHT;
        $chartH = self::H - self::PAD_TOP  - self::PAD_BOTTOM;
        $max    = max($values);
        $n      = count($values);
        $gap    = 8;
        $barW   = max(4, floor(($chartW - $gap * ($n + 1)) / max(1, $n)));

        $svg = $this->openSvg();
        $svg .= $this->axes($max, 5);

        foreach ($values as $i => $val) {
            $x     = self::PAD_LEFT + $gap + $i * ($barW + $gap);
            $bh    = (int) ($val / $max * $chartH);
            $y     = self::PAD_TOP + $chartH - $bh;
            $color = self::COLORS[$i % count(self::COLORS)];

            $svg .= "<rect x='{$x}' y='{$y}' width='{$barW}' height='{$bh}' fill='{$color}' rx='3'/>";
            if ($bh > 15) {
                $tx = (int) ($x + $barW / 2);
                $ty = (int) ($y + $bh - 5);
                $svg .= "<text x='{$tx}' y='{$ty}' text-anchor='middle' fill='white' font-size='9' font-family='Arial'>{$val}</text>";
            }
            $lx = (int) ($x + $barW / 2);
            $ly = self::PAD_TOP + $chartH + 15;
            $lbl = mb_strimwidth((string)($labels[$i] ?? ''), 0, 10, '…');
            $svg .= "<text x='{$lx}' y='{$ly}' text-anchor='middle' fill='#374151' font-size='9' font-family='Arial'>{$this->esc($lbl)}</text>";
        }

        $svg .= $this->closeSvg();
        return $svg;
    }

    public function lineChart(array $labels, array $values): string
    {
        if (empty($values) || max($values) === 0) {
            return $this->empty();
        }

        $chartW = self::W - self::PAD_LEFT - self::PAD_RIGHT;
        $chartH = self::H - self::PAD_TOP  - self::PAD_BOTTOM;
        $max    = max($values) ?: 1;
        $n      = count($values);

        $svg = $this->openSvg();
        $svg .= $this->axes($max, 5);

        $pts = [];
        foreach ($values as $i => $val) {
            $x    = self::PAD_LEFT + ($n > 1 ? $i / ($n - 1) : 0.5) * $chartW;
            $y    = self::PAD_TOP  + $chartH - ($val / $max * $chartH);
            $pts[] = "{$x},{$y}";
        }

        $fillPts = $pts;
        $lastX = self::PAD_LEFT + $chartW;
        $lastY = self::PAD_TOP + $chartH;
        $firstX = self::PAD_LEFT;
        $fillPts[] = "{$lastX},{$lastY}";
        $fillPts[] = "{$firstX},{$lastY}";

        $svg .= "<polygon points='" . implode(' ', $fillPts) . "' fill='rgba(99,102,241,0.15)'/>";
        $svg .= "<polyline points='" . implode(' ', $pts) . "' fill='none' stroke='#6366f1' stroke-width='2'/>";

        $step = max(1, (int) floor($n / 12));
        foreach ($values as $i => $val) {
            if ($i % $step !== 0 && $i !== $n - 1) {
                continue;
            }
            $x   = self::PAD_LEFT + ($n > 1 ? $i / ($n - 1) : 0.5) * $chartW;
            $y   = self::PAD_TOP  + $chartH - ($val / $max * $chartH);
            $svg .= "<circle cx='{$x}' cy='{$y}' r='3' fill='#6366f1'/>";
            $lbl  = mb_strimwidth((string)($labels[$i] ?? ''), 0, 8, '…');
            $ly   = self::PAD_TOP + $chartH + 15;
            $svg .= "<text x='{$x}' y='{$ly}' text-anchor='middle' fill='#374151' font-size='9' font-family='Arial'>{$this->esc($lbl)}</text>";
        }

        $svg .= $this->closeSvg();
        return $svg;
    }

    public function groupedBarChart(array $labels, array $datasets): string
    {
        if (empty($datasets) || empty($labels)) {
            return $this->empty();
        }

        $chartW   = self::W - self::PAD_LEFT - self::PAD_RIGHT;
        $chartH   = self::H - self::PAD_TOP  - self::PAD_BOTTOM;
        $n        = count($labels);
        $dsCount  = count($datasets);
        $allVals  = array_merge(...array_column($datasets, 'data'));
        $max      = empty($allVals) ? 1 : (max($allVals) ?: 1);
        $groupW   = (int) ($chartW / max(1, $n));
        $barW     = max(4, (int) (($groupW - 10) / max(1, $dsCount)));

        $svg = $this->openSvg();
        $svg .= $this->axes($max, 5);

        foreach ($labels as $gi => $label) {
            $gx = self::PAD_LEFT + $gi * $groupW + 5;
            foreach ($datasets as $di => $dataset) {
                $val   = $dataset['data'][$gi] ?? 0;
                $x     = $gx + $di * $barW;
                $bh    = (int) ($val / $max * $chartH);
                $y     = self::PAD_TOP + $chartH - $bh;
                $color = $dataset['color'] ?? self::COLORS[$di % count(self::COLORS)];
                $svg  .= "<rect x='{$x}' y='{$y}' width='" . ($barW - 2) . "' height='{$bh}' fill='{$color}' rx='2'/>";
            }
            $lx  = (int) ($gx + $groupW / 2);
            $ly  = self::PAD_TOP + $chartH + 15;
            $lbl = mb_strimwidth((string)$label, 0, 12, '…');
            $svg .= "<text x='{$lx}' y='{$ly}' text-anchor='middle' fill='#374151' font-size='9' font-family='Arial'>{$this->esc($lbl)}</text>";
        }

        $legendY = self::H - 10;
        $lx      = self::PAD_LEFT;
        foreach ($datasets as $di => $dataset) {
            $color = $dataset['color'] ?? self::COLORS[$di % count(self::COLORS)];
            $svg  .= "<rect x='{$lx}' y='" . ($legendY - 8) . "' width='10' height='10' fill='{$color}'/>";
            $lx   += 12;
            $svg  .= "<text x='{$lx}' y='{$legendY}' fill='#374151' font-size='9' font-family='Arial'>{$this->esc($dataset['label'] ?? '')}</text>";
            $lx   += strlen($dataset['label'] ?? '') * 6 + 20;
        }

        $svg .= $this->closeSvg();
        return $svg;
    }

    private function axes(int $max, int $steps): string
    {
        $chartW = self::W - self::PAD_LEFT - self::PAD_RIGHT;
        $chartH = self::H - self::PAD_TOP  - self::PAD_BOTTOM;
        $svg    = '';

        for ($i = 0; $i <= $steps; $i++) {
            $val  = (int) round($max * $i / $steps);
            $y    = self::PAD_TOP + $chartH - ($i / $steps) * $chartH;
            $lx   = self::PAD_LEFT - 5;
            $svg .= "<line x1='" . self::PAD_LEFT . "' y1='{$y}' x2='" . (self::PAD_LEFT + $chartW) . "' y2='{$y}' stroke='#e5e7eb' stroke-width='1'/>";
            $svg .= "<text x='{$lx}' y='" . ((int)$y + 4) . "' text-anchor='end' fill='#6b7280' font-size='9' font-family='Arial'>{$val}</text>";
        }

        $svg .= "<line x1='" . self::PAD_LEFT . "' y1='" . self::PAD_TOP . "' x2='" . self::PAD_LEFT . "' y2='" . (self::PAD_TOP + $chartH) . "' stroke='#d1d5db' stroke-width='1'/>";
        $svg .= "<line x1='" . self::PAD_LEFT . "' y1='" . (self::PAD_TOP + $chartH) . "' x2='" . (self::PAD_LEFT + $chartW) . "' y2='" . (self::PAD_TOP + $chartH) . "' stroke='#d1d5db' stroke-width='1'/>";

        return $svg;
    }

    private function openSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . self::W . '" height="' . self::H . '" viewBox="0 0 ' . self::W . ' ' . self::H . '" style="background:white;">';
    }

    private function closeSvg(): string
    {
        return '</svg>';
    }

    private function empty(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . self::W . '" height="60" viewBox="0 0 ' . self::W . ' 60"><text x="' . (self::W / 2) . '" y="35" text-anchor="middle" fill="#9ca3af" font-size="14" font-family="Arial">No chart data available</text></svg>';
    }

    private function esc(string $str): string
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
