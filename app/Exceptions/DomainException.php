<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Base class for a business-rule violation.
 *
 * Distinguishes "this is not allowed by the domain" from "something broke".
 * A domain exception carries a message intended for the person who triggered
 * it, which is why userMessage() is separate from getMessage(): the latter is
 * free to carry detail useful in logs but unsuitable for a UI.
 *
 * 08_UI_UX_BLUEPRINT.md §7 requires the scanner to report specific causes --
 * "QR expired", "Outside geofence", "QR already used", "Location accuracy too
 * low" -- rather than a generic failure. Those become subclasses in Phase 4.
 * The contract is defined in Phase 0 so they all render the same way instead of
 * each inventing its own handling.
 */
abstract class DomainException extends RuntimeException
{
    /**
     * The message safe to show the person who triggered this.
     */
    abstract public function userMessage(): string;

    /**
     * 422 by default: the request was well-formed but a business rule refused it.
     */
    public function statusCode(): int
    {
        return 422;
    }

    /**
     * Extra context for the response, e.g. the geofence distance that failed.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [];
    }
}
