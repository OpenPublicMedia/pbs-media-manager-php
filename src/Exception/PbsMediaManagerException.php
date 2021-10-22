<?php


namespace OpenPublicMedia\PbsMediaManager\Exception;

use Exception;
use InvalidArgumentException;
use Throwable;

/**
 * Base class for library exceptions.
 *
 * @package OpenPublicMedia\PbsMediaManager\Exception
 */
class PbsMediaManagerException extends Exception
{
    /**
     * The message value is always JSON encoded as exceptions provided by this
     * library are meant to be handled by implementors for error information.
     *
     * @param mixed $message
     *   Error data.
     * @param int $code
     *   Error code.
     * @param Throwable|null $previous
     *   Previous exception to chain.
     */
    public function __construct($message, int $code = 0, Throwable $previous = null)
    {
        $json = json_encode($message);
        if (!$json) {
            throw new InvalidArgumentException(
                "Unable to encode exception message value: $message",
                $this->getCode(),
                $this
            );
        }
        parent::__construct($json, $code, $previous);
    }
}
