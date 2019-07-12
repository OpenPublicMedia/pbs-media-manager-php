<?php
declare(strict_types=1);


namespace OpenPublicMedia\PbsMediaManager\Query;

use Generator;
use IteratorAggregate;
use OpenPublicMedia\PbsMediaManager\Response\PagedResponse;

/**
 * Generator over the "data" property from a Media Manager API response.
 *
 * @package OpenPublicMedia\PbsMediaManager\Query
 */
class Results implements IteratorAggregate
{
    /**
     * @var PagedResponse
     */
    private $pagedResponse;

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
}
