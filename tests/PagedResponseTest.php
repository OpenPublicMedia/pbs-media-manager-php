<?php


namespace OpenPublicMedia\PbsMediaManager\Test;

use OpenPublicMedia\PbsMediaManager\Query\Results;

/**
 * Class PagedResponseTest
 *
 * @coversDefaultClass \OpenPublicMedia\PbsMediaManager\Response\PagedResponse
 *
 * @package OpenPublicMedia\PbsMediaManager\Test
 */
class PagedResponseTest extends TestCaseBase
{
    private function getTopics(): Results
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getTopics-1'));
        $this->mockHandler->append($this->jsonFixtureResponse('getTopics-2'));
        return $this->client->getShows();
    }

    public function testEmptyResults(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('emptyList'));
        $results = $this->client->getShows();
        $this->assertCount(0, $results);
    }

    public function testResults(): void
    {
        $result = $this->getTopics();
        foreach ($result as $item) {
            $this->assertIsObject($item);
        }
    }

    public function testNavigation(): void
    {
        $result = $this->getTopics();
        $pagedResponse = $result->getResponse();
        $this->assertIsObject($pagedResponse->current());
        $this->assertEquals(1, $pagedResponse->key());
        $pagedResponse->next();
        $this->assertEquals(2, $pagedResponse->key());
        $pagedResponse->rewind();
        $this->assertEquals(1, $pagedResponse->key());
    }

    /**
     * @covers ::count
     */
    public function testCount(): void
    {
        $result = $this->getTopics();
        $pagedResponse = $result->getResponse();
        $this->assertCount(2, $pagedResponse);
    }

    /**
     * @covers ::getTotalItemsCount
     */
    public function testGetTotalItemsCount(): void
    {
        $result = $this->getTopics();
        $pagedResponse = $result->getResponse();
        $this->assertEquals(92, $pagedResponse->getTotalItemsCount());
    }
}
