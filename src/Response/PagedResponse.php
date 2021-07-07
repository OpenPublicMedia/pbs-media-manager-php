<?php
declare(strict_types=1);


namespace OpenPublicMedia\PbsMediaManager\Response;

use Countable;
use Iterator;
use OpenPublicMedia\PbsMediaManager\Client;
use stdClass;

/**
 * Page-traversable response data from the Media Manager API in JSON format.
 *
 * @package OpenPublicMedia\PbsMediaManager\Response
 */
class PagedResponse implements Iterator, Countable
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
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $totalItemsCount;

    /**
     * @var bool
     */
    private $singlePage;

    /**
     * @var array
     */
    private $response;

    /**
     * PagedResponse constructor.
     *
     * @param Client $client
     *   API client used for requests.
     * @param string $endpoint
     *   Endpoint to query.
     * @param array $query
     *   Additional API query parameters.
     * @param int $start_page
     *   Starting page. This also acts as the first page for the Iterator so
     *   "first" may not necessarily mean page 1.
     */
    public function __construct(
        Client $client,
        string $endpoint,
        array $query = [],
        int $start_page = 1
    ) {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->query = $query;
        $this->singlePage = isset($this->query['page']);

        //If the "page" query parameter is set, this will only return a single
        // page response.
        if ($this->singlePage) {
            $this->first = $this->query['page'];
            $this->page = $this->query['page'];
        } else {
            $this->first = $start_page;
            $this->page = $start_page;
        }

        // Execute the initial query to init count data.
        $this->response = $this->execute();
    }

    /**
     * Executes an API query and update count data for the iterator.
     *
     * @return stdClass
     *   The full API response as an object.
     */
    private function execute(): stdClass
    {
        $response = $this->client->request(
            'get',
            $this->endpoint,
            ['query' => $this->query + ['page' => $this->page]]
        );
        $data = json_decode($response->getBody()->getContents());
        $this->next = $this->client::getNextPage($data);

        // Update page and item totals (for Countable support).
        if (isset($data->meta) && isset($data->meta->pagination)) {
            if ($this->singlePage) {
                $this->totalItemsCount = count($data->data);
                $this->count = 1;
                $this->next = null;
            } else {
                $this->totalItemsCount = $data->meta->pagination->count;
                $this->count = (int) ceil($this->totalItemsCount/$data->meta->pagination->per_page);
            }
        } else {
            $data->meta = new stdClass();
            $data->meta->pagination = new stdClass();
            $this->totalItemsCount = 0;
            $this->count = 0;
        }

        // Add current page to data.
        $data->meta->pagination->current_page = $this->page;

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function current(): stdClass
    {
        // Only run the API query if necessary.
        if ($this->response->meta->pagination->current_page != $this->page) {
            $this->response = $this->execute();
        }
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->page = $this->next;
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->page;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return !is_null($this->page);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->page = $this->first;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return int
     *   Number of objects (not pages) in the result set.
     */
    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    /**
     * @return bool
     *   Whether or not this is a single page response.
     */
    public function isSinglePage(): bool
    {
        return $this->singlePage;
    }
}
