<?php


namespace OpenPublicMedia\PbsMediaManager\Exception;

use GuzzleHttp\Psr7\Response;
use OpenPublicMedia\PbsMediaManager\Client;
use Throwable;

/**
 * Handle API response errors manually, as the API passes error information back
 * in JSON format in the response body. The Client passes the option
 * "http_errors" to the client to prevent Guzzle from throwing for HTTP errors.
 *
 * @see Client::__construct()
 *
 * @package OpenPublicMedia\PbsMediaManager\Exception
 */
class BadRequestException extends PbsMediaManagerException
{
    /**
     * Throws error data as an array (encoded JSON) keyed by a "detail" field.
     *
     * @param Response $response
     *   The API response.
     * @param int $code
     *   Error code. This value will only be used if it is not the default (0).
     *   In most cases, the $response HTTP status code will be used.
     * @param Throwable|null $previous
     *   Previous exception to chain.
     */
    public function __construct(Response $response, int $code = 0, Throwable $previous = null)
    {
        $data = json_decode($response->getBody()->getContents());
        if (!empty($data)) {
            $message = $data->detail ?? $data;
        } else {
            $message = $response->getReasonPhrase();
        }

        // Use the HTTP status code if $code is not set.
        if (empty($code)) {
            $code = $response->getStatusCode();
        }

        parent::__construct(['detail' => $message], $code, $previous);
    }
}
