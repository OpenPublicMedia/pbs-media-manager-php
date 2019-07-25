<?php


namespace OpenPublicMedia\PbsMediaManager\Test;

use Generator;
use OpenPublicMedia\PbsMediaManager\Response\PagedResponse;

/**
 * Class ResultsTest
 *
 * @coversDefaultClass \OpenPublicMedia\PbsMediaManager\Query\Results
 *
 * @package OpenPublicMedia\PbsMediaManager\Test
 */
class ResultsTest extends TestCaseBase
{
    private $seasonId = 'bd2cf784-bf4a-4638-a477-721dfb29b12e';

    /**
     * @covers ::count
     */
    public function testCount()
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getEpisodes'));
        $result = $this->client->getEpisodes($this->seasonId);
        $this->assertCount(8, $result);
    }

    /**
     * @covers ::getResponse
     */
    public function testGetResponse()
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getEpisodes'));
        $result = $this->client->getEpisodes($this->seasonId);
        $response = $result->getResponse();
        $this->assertInstanceOf(PagedResponse::class, $response);
    }

    /**
     * @covers ::getIterator
     */
    public function testGetIterator()
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getEpisodes'));
        $result = $this->client->getEpisodes($this->seasonId);
        $iterator = $result->getIterator();
        $this->assertInstanceOf(Generator::class, $iterator);
        foreach ($result as $item) {
            $this->assertIsObject($item);
            continue;
        }
    }
}
