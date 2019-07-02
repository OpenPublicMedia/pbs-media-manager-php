<?php


namespace OpenPublicMedia\PbsTvSchedulesService\Test;

use Generator;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OpenPublicMedia\PbsMediaManager\Client;
use OpenPublicMedia\PbsMediaManager\Query\Results;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class ClientTest
 *
 * @coversDefaultClass \OpenPublicMedia\PbsMediaManager\Client
 *
 * @package OpenPublicMedia\PbsTvSchedulesService\Test
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * Create client with mock handler.
     */
    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->client = new Client(
            'api_key',
            'secret',
            Client::LIVE,
            ['handler' => $this->mockHandler]
        );
    }

    /**
     * @param string $name
     *   Base file name for a JSON fixture file.
     *
     * @return Response
     *   Guzzle 200 response with JSON body content.
     */
    private function jsonResponse($name)
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/fixtures/' . $name . '.json')
        );
    }

    /**
     * Gets and verify contents of a Results Generator.
     *
     * @param Results $result
     *   Results from an API query.
     * @param string $type
     *   Expected type of the first Result object.
     */
    private function verifyGenerator($result, $type)
    {
        $generator = $result->getIterator();
        $this->assertInstanceOf(Generator::class, $generator);
        $first = $generator->current();
        $this->assertIsObject($first);
        $this->assertObjectHasAttribute('type', $first);
        $this->assertEquals($type, $first->type);
    }

    /**
     * Verifies standard API object response.
     *
     * @param object $result
     *   Results from an API query.
     * @param string $id
     *   Expected GUID of the object.
     * @param string $type
     *   Expected type of the object.
     */
    private function verifryObject($result, $id, $type)
    {
        $this->assertIsObject($result);
        $this->assertObjectHasAttribute('id', $result);
        $this->assertEquals($id, $result->id);
        $this->assertObjectHasAttribute('attributes', $result);
        $this->assertIsObject($result->attributes);
        $this->assertObjectHasAttribute('type', $result);
        $this->assertEquals($type, $result->type);
    }

    /**
     * @covers ::request
     */
    public function testGuzzleException()
    {
        $this->mockHandler->append(new RequestException(
            'Bad request.',
            new Request('GET', 'test'),
            new Response(400)
        ));
        $this->expectException(RuntimeException::class);
        $this->client->request('get', 'test');
    }

    /**
     * @covers ::request
     */
    public function testApiUnexpectedResponse()
    {
        $this->mockHandler->append(new Response(201));
        $this->expectException(RuntimeException::class);
        $this->client->request('get', 'test');
    }

    /**
     * @covers ::getOne
     */
    public function testGetOneNull()
    {
        $this->mockHandler->append($this->jsonResponse('notFound'));
        $result = $this->client->getOne('franchise', 'bad-id');
        $this->assertNull($result);
    }

    public function testGetFranchise()
    {
        $id = 'e08bf78d-e6a3-44b9-b356-8753d01c7327';
        $this->mockHandler->append($this->jsonResponse('getFranchise'));
        $result = $this->client->getFranchise($id);
        $this->verifryObject($result, $id, 'franchise');
    }

    public function testGetFranchises()
    {
        $this->mockHandler->append($this->jsonResponse('getFranchises'));
        $result = $this->client->getFranchises();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'franchise');
    }

    public function testGetShow()
    {
        $id = '2e5c2027-ec2e-4214-baa3-6ff6af56c8c3';
        $this->mockHandler->append($this->jsonResponse('getShow'));
        $result = $this->client->getShow($id);
        $this->verifryObject($result, $id, 'show');
    }

    public function testGetShows()
    {
        $this->mockHandler->append($this->jsonResponse('getShows'));
        $result = $this->client->getShows();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'show');
    }

    public function testGetCollection()
    {
        $id = '5f390495-54d1-4f0c-91e4-0e72b91fb759';
        $this->mockHandler->append($this->jsonResponse('getCollection'));
        $result = $this->client->getCollection($id);
        $this->verifryObject($result, $id, 'collection');
    }

    public function testGetCollections()
    {
        $show_id = '2e5c2027-ec2e-4214-baa3-6ff6af56c8c3';
        $this->mockHandler->append($this->jsonResponse('getCollections'));
        $result = $this->client->getCollections($show_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'collection');
    }

    public function testGetSpecial()
    {
        $id = 'c7708c4c-e7c1-4ecb-ad63-6d87c6baafa9';
        $this->mockHandler->append($this->jsonResponse('getSpecial'));
        $result = $this->client->getSpecial($id);
        $this->verifryObject($result, $id, 'special');
    }

    public function testGetSpecials()
    {
        $show_id = '2e5c2027-ec2e-4214-baa3-6ff6af56c8c3';
        $this->mockHandler->append($this->jsonResponse('getSpecials'));
        $result = $this->client->getSpecials($show_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'special');
    }

    public function testGetSeaon()
    {
        $id = 'bd2cf784-bf4a-4638-a477-721dfb29b12e';
        $this->mockHandler->append($this->jsonResponse('getSeason'));
        $result = $this->client->getSeason($id);
        $this->verifryObject($result, $id, 'season');
    }

    public function testGetSeaons()
    {
        $show_id = 'd9588363-71f8-466d-a520-0dd73c7bbd0e';
        $this->mockHandler->append($this->jsonResponse('getSeasons'));
        $result = $this->client->getSeasons($show_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'season');
    }

    public function testGetEpisode()
    {
        $id = '99aa15d6-946e-4acc-8d33-96eb173a26f7';
        $this->mockHandler->append($this->jsonResponse('getEpisode'));
        $result = $this->client->getEpisode($id);
        $this->verifryObject($result, $id, 'episode');
    }

    public function testGetEpisodes()
    {
        $season_id = 'bd2cf784-bf4a-4638-a477-721dfb29b12e';
        $this->mockHandler->append($this->jsonResponse('getEpisodes'));
        $result = $this->client->getEpisodes($season_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'episode');
    }

    public function testGetAsset()
    {
        $id = 'a2ab3573-5a7a-4551-bd62-2688b9ec793a';
        $this->mockHandler->append($this->jsonResponse('getAsset'));
        $result = $this->client->getAsset($id);
        $this->verifryObject($result, $id, 'asset');
    }

    public function testGetAssets()
    {
        $episode_id = 'd5cdd80c-4614-452b-91ed-cd4e583e0ef7';
        $this->mockHandler->append($this->jsonResponse('getAssets'));
        $result = $this->client->getAssets(['episode_id' => $episode_id]);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'asset');
    }

    public function testGetGenres()
    {
        $this->mockHandler->append($this->jsonResponse('getGenres'));
        $result = $this->client->getGenres();
        $this->assertIsArray($result);
        $this->assertCount(9, $result);
    }

    public function testGetTopics()
    {
        $this->mockHandler->append($this->jsonResponse('getTopics-1'));
        $this->mockHandler->append($this->jsonResponse('getTopics-2'));
        $result = $this->client->getTopics();
        $this->assertIsArray($result);
    }

    public function testGetChangelog()
    {
        $this->mockHandler->append($this->jsonResponse('getChangelog'));
        $result = $this->client->getChangelog();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        // Individual result types differ for the changelog endpoint.
        $this->verifyGenerator($result, 'asset');
    }

    public function testGetRemoteAssets()
    {
        $this->mockHandler->append($this->jsonResponse('getRemoteAssets'));
        $result = $this->client->getRemoteAssets();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'remoteasset');
    }
}
