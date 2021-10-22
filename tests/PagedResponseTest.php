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

    /**
     * Gets an example two-page response.
     *
     * @param array $query
     *   Query parameters to pass to the client.
     *
     * @return Results
     *   Generator of the mock results.
     * @throws \OpenPublicMedia\PbsMediaManager\Exception\BadRequestException
     */
    private function getChangelog(array $query = []): Results
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog-1'));
        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog-2'));
        return $this->client->getChangelog($query);
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

    /**
     * @covers ::isSinglePage
     */
    public function testIsSinglePage(): void
    {
        $result = $this->getChangelog();
        $response = $result->getResponse();
        $this->assertFalse($response->isSinglePage());
        $result = $this->getChangelog(['page' => 1]);
        $response = $result->getResponse();
        $this->assertTrue($response->isSinglePage());
    }

    public function testSinglePageQueryCount(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog-1'));
        $result = $this->client->getChangelog(['page' => 1]);
        $this->assertCount(50, $result);

        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog-2'));
        $result = $this->client->getChangelog(['page' => 2]);
        $this->assertCount(35, $result);
    }
}
