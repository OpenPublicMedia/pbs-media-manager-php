<?php


namespace OpenPublicMedia\PbsMediaManager\Response;

use Iterator;
use OpenPublicMedia\PbsMediaManager\Client;

/**
 * Page-traversable response data from the Media Manager API in JSON format.
 *
 * @package OpenPublicMedia\PbsMediaManager\Response
 */
class PagedResponse implements Iterator
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var array
     */
    private $query;

    /**
     * @var int
     */
    private $first;

    /**
     * @var int|null
     */
    private $next;

    /**
     * @var int
     */
    private $page;

    /**
     * PagedResponse constructor.
     *
     * @param Client $client
     *   API client used for requests.
     * @param string $endpoint
     *   Endpoint to query.
     * @param array $query
     *   Additional API query parameters.
     * @param int $page
     *   Starting page. This also acts as the first page for the Iterator so
     *   "first" may not necessarily mean page 1.
     */
    public function __construct(Client $client, $endpoint, $query = [], $page = 1)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->query = $query;
        $this->first = $page;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        $response = $this->client->request(
            'get',
            $this->endpoint,
            $this->query + ['page' => $this->page]
        );
        $json = json_decode($response->getBody());
        $this->next = $this->client::getNextPage($json);
        return $json;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->page = $this->next;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->page;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return !is_null($this->page);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->page = $this->first;
    }
}
