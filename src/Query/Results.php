<?php
declare(strict_types=1);


namespace OpenPublicMedia\PbsMediaManager\Query;

use Countable;
use Generator;
use IteratorAggregate;
use OpenPublicMedia\PbsMediaManager\Response\PagedResponse;

/**
 * Generator over the "data" property from a Media Manager API response.
 *
 * @package OpenPublicMedia\PbsMediaManager\Query
 */
class Results implements IteratorAggregate, Countable
{
    private PagedResponse $pagedResponse;

    /**
     * ObjectsResponse constructor.
     *
     * @param PagedResponse $pagedResponse
     */
    public function __construct(PagedResponse $pagedResponse)
    {
        $this->pagedResponse = $pagedResponse;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Generator
    {
        foreach ($this->pagedResponse as $response) {
            foreach ($response->data as $object) {
                yield $object->id => $object;
            }
        }
    }

    /**
     * Gets the response object being iterated.
     *
     * @return PagedResponse
     */
    public function getResponse(): PagedResponse
    {
        return $this->pagedResponse;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->pagedResponse->getTotalItemsCount();
    }
}
