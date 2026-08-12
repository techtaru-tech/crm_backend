<?php

namespace App\Services;

/**
 * Simple .env file editor — reads, updates, and writes key=value pairs.
 * NEVER logs or exposes secret values.
 */
class EnvEditor
{
    private string $path;

    public function __construct(?string $envPath = null)
    {
        $this->path = $envPath ?? base_path('.env');
    }

    public function get(string $key): ?string
    {
        if (! file_exists($this->path)) {
            return null;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                if (trim($k) === $key) {
                    return trim($v, " \t\"'");
                }
            }
        }
        return null;
    }

    public function set(string $key, string $value): void
    {
        // Strip characters that would break a .env line or allow an
        // attacker / careless paste to inject additional environment
        // variables.  Same scrub install.php's sanitiseEnvValue() does:
        // an embedded "\n" would smuggle a fresh `KEY=value\n` line
        // into the file, overriding settings further down (re-enabling
        // APP_DEBUG, swapping QUEUE_CONNECTION, planting a bogus
        // APP_KEY).  Backslashes and double-quotes break our
        // quoted-value lines.  NUL + other control bytes are stripped
        // because nothing in a real env value needs them.
        $value = preg_replace('/[\r\n"\\\\\x00-\x1F]/', '', $value);

        $envPath = $this->path;

        if (! file_exists($envPath)) {
            file_put_contents($envPath, "{$key}={$value}\n");
            return;
        }

        $contents = file_get_contents($envPath);
        $escaped  = addcslashes($value, '"\\');
        $pattern  = "/^{$key}=.*$/m";
        $replacement = "{$key}=\"{$escaped}\"";

        if (preg_match($pattern, $contents)) {
            $contents = preg_replace($pattern, $replacement, $contents);
        } else {
            $contents .= "\n{$replacement}";
        }

        file_put_contents($envPath, $contents);
    }
}
