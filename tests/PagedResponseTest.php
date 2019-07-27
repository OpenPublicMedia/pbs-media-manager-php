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
    private function getChangelog(): Results
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog-1'));
        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog-2'));
        return $this->client->getChangelog();
    }

    public function testEmptyResults(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('emptyList'));
        $results = $this->client->getShows();
        $this->assertCount(0, $results);
    }

    public function testResults(): void
    {
        $result = $this->getChangelog();
        foreach ($result as $item) {
            $this->assertIsObject($item);
        }
    }

    public function testNavigation(): void
    {
        $result = $this->getChangelog();
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
        $result = $this->getChangelog();
        $pagedResponse = $result->getResponse();
        $this->assertCount(2, $pagedResponse);
    }

    /**
     * @covers ::getTotalItemsCount
     */
    public function testGetTotalItemsCount(): void
    {
        $result = $this->getChangelog();
        $pagedResponse = $result->getResponse();
        $this->assertEquals(85, $pagedResponse->getTotalItemsCount());
    }
}
