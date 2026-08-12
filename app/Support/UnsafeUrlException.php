<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Thrown by {@see UrlSafetyGuard::assertSafe()} when a tenant-supplied
 * URL would target a blocked destination (RFC1918, loopback, metadata
 * IP, unsupported scheme, unresolvable host).
 *
 * Distinct exception class so callers can `catch (UnsafeUrlException)`
 * specifically — distinguishing an SSRF rejection from a generic HTTP
 * client error.
 */
final class UnsafeUrlException extends \RuntimeException
{
}
