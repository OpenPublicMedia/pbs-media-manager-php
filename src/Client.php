<?php


namespace OpenPublicMedia\PbsMediaManager;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use League\Uri\Components\Query;
use League\Uri\Parser;
use League\Uri\Parser\QueryString;
use OpenPublicMedia\PbsMediaManager\Query\Results;
use OpenPublicMedia\PbsMediaManager\Response\PagedResponse;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * PBS Media Manager API Client.
 *
 * @url https://docs.pbs.org/display/CDA
 *
 * @package OpenPublicMedia\PbsMediaManager
 */
class Client
{
    /**
     * Live base URL for the API.
     *
     * @url https://docs.pbs.org/display/CDA/Resources#Resources-BaseEndpoint
     */
    const LIVE = "https://media.services.pbs.org/api/v1/";

    /**
     * Test base URL for the API.
     *
     * @url https://docs.pbs.org/display/CDA/Resources#Resources-TestingEnvironment
     */
    const STAGING = "https://media-staging.services.pbs.org/api/v1/";

    /**
     * The maximum number of items the API will return.
     *
     * @url https://docs.pbs.org/display/CDA/Pagination#Pagination-Pagination
     */
    const MAX_PAGE_SIZE = 50;

    /**
     * Client for handling API requests
     *
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param string $key
     *   API client key.
     * @param string $secret
     *   API client secret.
     * @param string $base_uri
     *   Base API URI.
     */
    public function __construct($key, $secret, $base_uri = self::LIVE)
    {
        $this->client = new GuzzleClient(
            [
                'base_uri' => $base_uri,
                'auth' => [$key, $secret]
            ]
        );
    }

    /**
     * @param string $method
     *   Request method (e.g. 'get', 'post', 'put', etc.).
     * @param string $endpoint
     *   API endpoint to query.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return ResponseInterface
     *   Response data from the API.
     */
    public function request($method, $endpoint, array $query = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, [
                'query' => self::buildQuery($query)
            ]);
        } catch (GuzzleException $e) {
            // Implementors should handle this exception as the API responds 404 for invalid IDs.
            throw new RuntimeException($e->getMessage());
        }
        /* @url https://docs.pbs.org/display/CDA/HTTP+Response+Status+Codes */
        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 204) {
            throw new RuntimeException($response->getReasonPhrase());
        }
        return $response;
    }

    /**
     * Gets an iterator for paging through API responses.
     *
     * @param $endpoint
     *   URL to query.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return Results
     *   Generator of the API query results.
     */
    public function get($endpoint, array $query = [])
    {
        $response = new PagedResponse($this, $endpoint, $query);
        return new Results($response);
    }

    /**
     * Gets a complete list of objects by paging through all results.
     *
     * @param $endpoint
     *   URL to query.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return array
     *   All data returned from the API.
     */
    public function getAll($endpoint, array $query = [])
    {
        $results = [];
        $response = new PagedResponse($this, $endpoint, $query);
        foreach ($response as $page) {
            array_push($results, ...$page->data);
        }
        return $results;
    }

    /**
     * Gets the a single object by ID from an API request.
     *
     * @param $endpoint
     *   URL to query.
     * @param $id
     *   GUID of an API object.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return object|null
     *   Single object record from the API or null.
     */
    public function getOne($endpoint, $id, array $query = [])
    {
        $response = $this->request('get', $endpoint . '/' . $id, $query);
        $json = json_decode($response->getBody());
        if (!empty($json->data)) {
            return $json->data;
        }
        return null;
    }

    /**
     * Searches a JSON response for a link containing next page information.
     *
     * @param $json
     *   A full response from the API.
     *
     * @return int|null
     *   The number of the next page or null if there is no next page.
     */
    public static function getNextPage($json)
    {
        $page = null;
        if (isset($json->links) && isset($json->links->next)
            && !empty($json->links->next)) {
            $parser = new Parser();
            $query = $parser($json->links->next)['query'];
            $page = (int) QueryString::extract($query)['page'];
        }
        return $page;
    }

    /**
     * Creates a query string from an array of parameters.
     *
     * The parameter "fetch-related" is provided as a default here in order to
     * include related objects for most queries. E.g. Episodes will also have
     * related Assets instead of requiring a separate call to the Episode's
     * Assets endpoint.
     *
     * @url https://docs.pbs.org/display/CDA/Episodes#Episodes-QueryParameters
     * @url https://docs.pbs.org/display/CDA/Pagination#Pagination-Pagination
     *
     * @param array $parameters
     *   Query parameters keyed to convert to "key=value".
     *
     * @return string
     *   All parameters as a string.
     */
    public static function buildQuery(array $parameters)
    {
        $parameters += [
            'fetch-related' => true,
            'page-size' => self::MAX_PAGE_SIZE,
        ];
        $query = Query::createFromPairs($parameters);
        return (string) $query;
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Franchises
     *
     * @param $id
     *   GUID of a Franchise.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Franchise or null
     */
    public function getFranchise($id, array $query = [])
    {
        return $this->getOne('franchises/', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Franchises
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Franchises.
     */
    public function getFranchises(array $query = [])
    {
        return $this->get('franchises', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Shows
     *
     * @param $id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Show or null
     */
    public function getShow($id, array $query = [])
    {
        return $this->getOne('shows/', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Shows
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Shows.
     */
    public function getShows(array $query = [])
    {
        return $this->get('shows', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Collections
     *
     * @param $id
     *   GUID of a Collection.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Collection or null
     */
    public function getCollection($id, array $query = [])
    {
        return $this->getOne('collections', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Collections
     *
     * @param $show_id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Collections belonging to the Show.
     */
    public function getCollections($show_id, array $query = [])
    {
        return $this->get('shows/' . $show_id . '/collections', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Specials
     *
     * @param $id
     *   GUID of a Special.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Special or null
     */
    public function getSpecial($id, array $query = [])
    {
        return $this->getOne('specials', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Specials
     *
     * @param $show_id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Specials belonging to the Show.
     */
    public function getSpecials($show_id, array $query = [])
    {
        return $this->get('shows/' . $show_id . '/specials', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Seasons
     *
     * @param $id
     *   GUID of a Season.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Season or null
     */
    public function getSeason($id, array $query = [])
    {
        return $this->getOne('seasons/', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Seasons
     *
     * @param $show_id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Seasons belonging to the Show.
     */
    public function getSeasons($show_id, array $query = [])
    {
        return $this->get('shows/' . $show_id . '/seasons', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Episodes
     *
     * @param $id
     *   GUID of a Episode.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Episode or null
     */
    public function getEpisode($id, array $query = [])
    {
        return $this->getOne('episodes/', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Episodes
     *
     * @param $season_id
     *   GUID of a Season.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Episodes belonging to the Season.
     */
    public function getEpisodes($season_id, array $query = [])
    {
        return $this->get('seasons/' . $season_id . '/episodes', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Assets
     *
     * @param $id
     *   GUID of an Asset.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return object|null
     *   Asset or null
     */
    public function getAsset($id, array $query = [])
    {
        return $this->getOne('assets/', $id, $query);
    }

    /**
     * One of the following query parameters must be used when querying this
     * endpoint: special-id, show-slug, slug, episode-id, available, show-id,
     * special-slug, episode-slug, tp-media-id, id.
     *
     * @url https://docs.pbs.org/display/CDA/Assets
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Assets satisfying the query parameters.
     */
    public function getAssets(array $query)
    {
        return $this->get('assets', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Shows#Shows-genreTableGenreList
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return array
     *   All Genres.
     */
    public function getGenres(array $query = [])
    {
        return $this->getAll('genres', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Topics
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return array
     *   All Topics.
     */
    public function getTopics(array $query = [])
    {
        return $this->getAll('topics', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Changelog+Endpoint
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Changelog entries.
     */
    public function getChangelog(array $query = [])
    {
        return $this->get('changelog', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Remote+Assets
     *
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Remote Assets.
     */
    public function getRemoteAssets(array $query = [])
    {
        return $this->get('remote-assets', $query);
    }
}
