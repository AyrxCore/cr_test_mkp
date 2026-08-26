<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Thrown when the X-Channel header is missing or invalid.
 * Filtered from Sentry — this is a client-side error (missing/wrong header).
 */
final class MissingChannelHeaderException extends BadRequestHttpException
{
}
