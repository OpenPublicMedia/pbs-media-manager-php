<?php
declare(strict_types=1);


namespace OpenPublicMedia\PbsMediaManager\Test;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OpenPublicMedia\PbsMediaManager\Query\Results;
use RuntimeException;

/**
 * Class ClientTest
 *
 * @coversDefaultClass \OpenPublicMedia\PbsMediaManager\Client
 *
 * @package OpenPublicMedia\PbsMediaManager\Test
 */
class ClientTest extends TestCaseBase
{
    /**
     * @covers ::request
     */
    public function testGuzzleException(): void
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
    public function testApiUnexpectedResponse(): void
    {
        $this->mockHandler->append(new Response(201));
        $this->expectException(RuntimeException::class);
        $this->client->request('get', 'test');
    }

    /**
     * @covers ::getOne
     */
    public function testGetOneNull(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('notFound'));
        $result = $this->client->getOne('franchise', 'bad-id');
        $this->assertNull($result);
    }

    public function testGetFranchise(): void
    {
        $id = 'e08bf78d-e6a3-44b9-b356-8753d01c7327';
        $this->mockHandler->append($this->jsonFixtureResponse('getFranchise'));
        $result = $this->client->getFranchise($id);
        $this->verifryObject($result, $id, 'franchise');
    }

    public function testGetFranchises(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getFranchises'));
        $result = $this->client->getFranchises();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'franchise');
    }

    public function testGetShow(): void
    {
        $id = '2e5c2027-ec2e-4214-baa3-6ff6af56c8c3';
        $this->mockHandler->append($this->jsonFixtureResponse('getShow'));
        $result = $this->client->getShow($id);
        $this->verifryObject($result, $id, 'show');
    }

    public function testGetShows(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getShows'));
        $result = $this->client->getShows();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'show');
    }

    public function testGetCollection(): void
    {
        $id = '5f390495-54d1-4f0c-91e4-0e72b91fb759';
        $this->mockHandler->append($this->jsonFixtureResponse('getCollection'));
        $result = $this->client->getCollection($id);
        $this->verifryObject($result, $id, 'collection');
    }

    public function testGetCollections(): void
    {
        $show_id = '2e5c2027-ec2e-4214-baa3-6ff6af56c8c3';
        $this->mockHandler->append($this->jsonFixtureResponse('getCollections'));
        $result = $this->client->getCollections($show_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'collection');
    }

    public function testGetSpecial(): void
    {
        $id = 'c7708c4c-e7c1-4ecb-ad63-6d87c6baafa9';
        $this->mockHandler->append($this->jsonFixtureResponse('getSpecial'));
        $result = $this->client->getSpecial($id);
        $this->verifryObject($result, $id, 'special');
    }

    public function testGetSpecials(): void
    {
        $show_id = '2e5c2027-ec2e-4214-baa3-6ff6af56c8c3';
        $this->mockHandler->append($this->jsonFixtureResponse('getSpecials'));
        $result = $this->client->getSpecials($show_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'special');
    }

    public function testGetSeaon(): void
    {
        $id = 'bd2cf784-bf4a-4638-a477-721dfb29b12e';
        $this->mockHandler->append($this->jsonFixtureResponse('getSeason'));
        $result = $this->client->getSeason($id);
        $this->verifryObject($result, $id, 'season');
    }

    public function testGetSeaons(): void
    {
        $show_id = 'd9588363-71f8-466d-a520-0dd73c7bbd0e';
        $this->mockHandler->append($this->jsonFixtureResponse('getSeasons'));
        $result = $this->client->getSeasons($show_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'season');
    }

    public function testGetEpisode(): void
    {
        $id = '99aa15d6-946e-4acc-8d33-96eb173a26f7';
        $this->mockHandler->append($this->jsonFixtureResponse('getEpisode'));
        $result = $this->client->getEpisode($id);
        $this->verifryObject($result, $id, 'episode');
    }

    public function testGetEpisodes(): void
    {
        $season_id = 'bd2cf784-bf4a-4638-a477-721dfb29b12e';
        $this->mockHandler->append($this->jsonFixtureResponse('getEpisodes'));
        $result = $this->client->getEpisodes($season_id);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'episode');
    }

    public function testGetAsset(): void
    {
        $id = 'a2ab3573-5a7a-4551-bd62-2688b9ec793a';
        $this->mockHandler->append($this->jsonFixtureResponse('getAsset'));
        $result = $this->client->getAsset($id);
        $this->verifryObject($result, $id, 'asset');
    }

    public function testGetAssets(): void
    {
        $episode_id = 'd5cdd80c-4614-452b-91ed-cd4e583e0ef7';
        $this->mockHandler->append($this->jsonFixtureResponse('getAssets'));
        $result = $this->client->getAssets(['episode_id' => $episode_id]);
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'asset');
    }

    public function testGetGenres(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getGenres'));
        $result = $this->client->getGenres();
        $this->assertIsArray($result);
        $this->assertCount(9, $result);
    }

    public function testGetTopics(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getTopics-1'));
        $this->mockHandler->append($this->jsonFixtureResponse('getTopics-2'));
        $result = $this->client->getTopics();
        $this->assertIsArray($result);
    }

    public function testGetChangelog(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getChangelog'));
        $result = $this->client->getChangelog();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        // Individual result types differ for the changelog endpoint.
        $this->verifyGenerator($result, 'asset');
    }

    public function testGetRemoteAssets(): void
    {
        $this->mockHandler->append($this->jsonFixtureResponse('getRemoteAssets'));
        $result = $this->client->getRemoteAssets();
        $this->assertInstanceOf(Results::class, $result);
        $this->assertObjectHasAttribute('pagedResponse', $result);
        $this->verifyGenerator($result, 'remoteasset');
    }
}
