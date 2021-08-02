<?php
declare(strict_types=1);


namespace OpenPublicMedia\PbsMediaManager;

use DateTimeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use League\Uri\Components\Query;
use League\Uri\Parser;
use League\Uri\Parser\QueryString;
use OpenPublicMedia\PbsMediaManager\Exception\BadRequestException;
use OpenPublicMedia\PbsMediaManager\Query\Results;
use OpenPublicMedia\PbsMediaManager\Response\PagedResponse;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use stdClass;

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
     * @param array $options
     *   Additional options to pass to Guzzle client.
     */
    public function __construct(
        string $key,
        string $secret,
        string $base_uri = self::LIVE,
        array $options = []
    ) {
        $options = [
            'base_uri' => $base_uri,
            'auth' => [$key, $secret],
            'http_errors' => false
        ] + $options;
        $this->client = new GuzzleClient($options);
    }

    /**
     * Sends a request to the API.
     *
     * @param string $method
     *   Request method (e.g. 'get', 'post', 'put', etc.).
     * @param string $endpoint
     *   API endpoint to query.
     * @param array $options
     *   Additional options to pass with the request.
     *
     * @return \Psr\Http\Message\ResponseInterface
     *   Response data from the API.
     *
     * @throws \OpenPublicMedia\PbsMediaManager\Exception\BadRequestException
     */
    public function request(string $method, string $endpoint, array $options = []): ResponseInterface
    {
        if ($method === 'get' && isset($options['query'])) {
            $options['query'] = self::buildGetQuery($options['query']);
        }

        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        /* @url https://docs.pbs.org/display/CDA/HTTP+Response+Status+Codes */
        switch ($response->getStatusCode()) {
            case 200:
            case 204:
                break;
            case 400:
            case 401:
            case 403:
            case 404:
            case 409:
            case 500:
                throw new BadRequestException($response);
            default:
                throw new RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response;
    }

    /*
     * GET methods.
     */

    /**
     * Gets an iterator for paging through API responses.
     *
     * @param string $endpoint
     *   URL to query.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return Results
     *   Generator of the API query results.
     */
    public function get(string $endpoint, array $query = []): Results
    {
        $response = new PagedResponse($this, $endpoint, $query);
        return new Results($response);
    }

    /**
     * Gets a complete list of objects by paging through all results.
     *
     * @param string $endpoint
     *   URL to query.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return array
     *   All data returned from the API.
     */
    public function getAll(string $endpoint, array $query = []): array
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
     * @param string $endpoint
     *   URL to query.
     * @param string $id
     *   GUID of an API object.
     * @param array $query
     *   Additional query parameters in the form `param => value`.
     *
     * @return stdClass|null
     *   Single object record from the API or null.
     */
    public function getOne(string $endpoint, string $id, array $query = []): ?stdClass
    {
        try {
            $response = $this->request('get', $endpoint . '/' . $id, ['query' => $query]);
        } catch (BadRequestException $e) {
            return null;
        }

        $data = json_decode($response->getBody()->getContents());
        if (!empty($data->data)) {
            return $data->data;
        }

        return null;
    }

    /**
     * Searches a JSON response for a link containing next page information.
     *
     * @param stdClass $response
     *   A full response from the API.
     *
     * @return int|null
     *   The number of the next page or null if there is no next page.
     */
    public static function getNextPage(stdClass $response): ?int
    {
        $page = null;
        if (isset($response->links) && isset($response->links->next)
            && !empty($response->links->next)) {
            $parser = new Parser();
            $query = $parser($response->links->next)['query'];
            $page = (int) QueryString::extract($query)['page'];
        }
        return $page;
    }

    /**
     * Creates a query string from an array of parameters for a GET request.
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
    public static function buildGetQuery(array $parameters): string
    {
        $parameters += [
            'fetch-related' => true,
            'page-size' => self::MAX_PAGE_SIZE,
        ];
        // Remove any empty parameters.
        $parameters = array_filter($parameters);
        $query = Query::createFromParams($parameters);
        return (string) $query;
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Franchises
     *
     * @param string $id
     *   GUID of a Franchise.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Franchise or null
     */
    public function getFranchise(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('franchises', $id, $query);
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
    public function getFranchises(array $query = []): Results
    {
        return $this->get('franchises', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Search+Franchises
     *
     * @param string $search_term
     *   Search term.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return \OpenPublicMedia\PbsMediaManager\Query\Results
     */
    public function searchFranchises(string $search_term, array $query = []): Results
    {
        $query['query'] = $search_term;
        return $this->get('franchises/search', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Shows
     *
     * @param string $id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Show or null
     */
    public function getShow(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('shows', $id, $query);
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
    public function getShows(array $query = []): Results
    {
        return $this->get('shows', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Search+Shows
     *
     * @param string $search_term
     *   Search term.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return \OpenPublicMedia\PbsMediaManager\Query\Results
     */
    public function searchShows(string $search_term, array $query = []): Results
    {
        $query['query'] = $search_term;
        return $this->get('shows/search', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Collections
     *
     * @param string $id
     *   GUID of a Collection.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Collection or null
     */
    public function getCollection(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('collections', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Collections
     *
     * @param string $show_id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Collections belonging to the Show.
     */
    public function getCollections(string $show_id, array $query = []): Results
    {
        return $this->get('shows/' . $show_id . '/collections', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Specials
     *
     * @param string $id
     *   GUID of a Special.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Special or null
     */
    public function getSpecial(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('specials', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Specials
     *
     * @param string $show_id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Specials belonging to the Show.
     */
    public function getSpecials(string $show_id, array $query = []): Results
    {
        return $this->get('shows/' . $show_id . '/specials', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Search+Specials
     *
     * @param string $search_term
     *   Search term.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return \OpenPublicMedia\PbsMediaManager\Query\Results
     */
    public function searchSpecials(string $search_term, array $query = []): Results
    {
        $query['query'] = $search_term;
        return $this->get('specials/search', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Seasons
     *
     * @param string $id
     *   GUID of a Season.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Season or null
     */
    public function getSeason(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('seasons', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Seasons
     *
     * @param string $show_id
     *   GUID of a Show.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Seasons belonging to the Show.
     */
    public function getSeasons(string $show_id, array $query = []): Results
    {
        return $this->get('shows/' . $show_id . '/seasons', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Episodes
     *
     * @param string $id
     *   GUID of a Episode.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Episode or null
     */
    public function getEpisode(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('episodes', $id, $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Episodes
     *
     * @param string $season_id
     *   GUID of a Season.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return Results
     *   Generator of Episodes belonging to the Season.
     */
    public function getEpisodes(string $season_id, array $query = []): Results
    {
        return $this->get('seasons/' . $season_id . '/episodes', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Search+Episodes
     *
     * @param string $search_term
     *   Search term.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return \OpenPublicMedia\PbsMediaManager\Query\Results
     */
    public function searchEpisodes(string $search_term, array $query = []): Results
    {
        $query['query'] = $search_term;
        return $this->get('episodes/search', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Assets
     *
     * @param string $id
     *   GUID of an Asset.
     * @param array $query
     *   Additional API query parameters.
     *
     * @return stdClass|null
     *   Asset or null
     */
    public function getAsset(string $id, array $query = []): ?stdClass
    {
        return $this->getOne('assets', $id, $query);
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
    public function getAssets(array $query): Results
    {
        return $this->get('assets', $query);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Search+Assets
     *
     * Asset search supports a much broader set of arguments so this base method
     * only accepts query arguments.
     *
     * @param array $query
     *   Search query parameters.
     *
     * @return \OpenPublicMedia\PbsMediaManager\Query\Results
     */
    public function searchAssets(array $query): Results
    {
        // The assets search endpoint does not support `fetch-related`.
        $query['fetch-related'] = null;
        return $this->get('assets/search', $query);
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
    public function getGenres(array $query = []): array
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
    public function getTopics(array $query = []): array
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
    public function getChangelog(array $query = []): Results
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
    public function getRemoteAssets(array $query = []): Results
    {
        return $this->get('remote-assets', $query);
    }


    /*
     * POST methods.
     */

    /**
     * Sends a POST API request.
     *
     * @param string $endpoint
     *   URL to send the POST request to.
     * @param array $data
     *   Data to include in the POST body as JSON.
     *
     * @return string
     *   ID of created object.
     */
    public function post(string $endpoint, array $data): string
    {
        $response = $this->request('post', $endpoint, ['json' => $data]);
        // @todo Handle response and return _just_ GUID.
        return $response->getHeader('location')[0];
    }

    /**
     * Add a season to a show (undocumented endpoint).
     *
     * @param string $show_id
     * @param int $ordinal
     * @param string|null $title
     * @param string|null $description_short
     * @param string|null $description_long
     *
     * @return string
     *   ID of added season.
     */
    public function addSeason(
        string $show_id,
        int $ordinal,
        ?string $title = null,
        ?string $description_short = null,
        ?string $description_long = null
    ): string {
        $attributes = array_filter([
            'ordinal' => $ordinal,
            'title' => $title ?? null,
            'description_short' => $description_short ?? null,
            'description_long' => $description_long ?? null,
        ]);

        return $this->post(
            "shows/$show_id/seasons/",
            ['data' => ['type' => 'season', 'attributes' => $attributes]]
        );
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Create+Episode
     *
     * @param string $season_id
     * @param string $title
     * @param string $description_short
     * @param string $description_long
     * @param int|null $ordinal
     * @param string|null $segment
     * @param string|null $title_sortable
     * @param string|null $nola
     * @param string|null $language
     * @param \DateTimeInterface|null $premiered_on
     * @param \DateTimeInterface|null $encored_on
     *
     * @return string
     *   ID of added episode.
     */
    public function addEpisode(
        string $season_id,
        string $title,
        string $description_short,
        string $description_long,
        ?int $ordinal = null,
        ?string $segment = null,
        ?string $title_sortable = null,
        ?string $nola = null,
        ?string $language = null,
        ?DateTimeInterface $premiered_on = null,
        ?DateTimeInterface $encored_on = null
    ): string {
        $attributes = array_filter([
            'title' => $title,
            'description_short' => $description_short,
            'description_long' => $description_long,
            'ordinal' => $ordinal ?? null,
            'segment' => $segment ?? null,
            'title_sortable' => $title_sortable ?? null,
            'nola' => $nola ?? null,
            'language' => $language ?? null,
            'premiered_on' => ($premiered_on ? $premiered_on->format('Y-m-d') : null),
            'encored_on' => ($encored_on ? $encored_on->format('Y-m-d') : null),
        ]);

        return $this->post(
            "seasons/$season_id/episodes/",
            ['data' => ['type' => 'episode', 'attributes' => $attributes]]
        );
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Create+Special
     *
     * @param string $show_id
     * @param string $title
     * @param string $description_short
     * @param string $description_long
     * @param string|null $title_sortable
     * @param string|null $nola
     * @param string|null $language
     * @param \DateTimeInterface|null $premiered_on
     * @param \DateTimeInterface|null $encored_on
     *
     * @return string
     *   ID of added special.
     */
    public function addSpecial(
        string $show_id,
        string $title,
        string $description_short,
        string $description_long,
        ?string $title_sortable = null,
        ?string $nola = null,
        ?string $language = null,
        ?DateTimeInterface $premiered_on = null,
        ?DateTimeInterface $encored_on = null
    ): string {
        $attributes = array_filter([
            'title' => $title,
            'description_short' => $description_short,
            'description_long' => $description_long,
            'title_sortable' => $title_sortable ?? null,
            'nola' => $nola ?? null,
            'language' => $language ?? null,
            'premiered_on' => ($premiered_on ? $premiered_on->format('Y-m-d') : null),
            'encored_on' => ($encored_on ? $encored_on->format('Y-m-d') : null),
        ]);

        return $this->post(
            "shows/$show_id/specials/",
            ['data' => ['type' => 'special', 'attributes' => $attributes]]
        );
    }

    /*
     * PATCH methods.
     */

    /**
     * Sends a PATCH API request.
     *
     * @param string $endpoint
     *   URL to send the PATCH request to.
     * @param array $data
     *   Data to include in the PATCH body as JSON.
     */
    public function patch(string $endpoint, array $data): void
    {
        $this->request('patch', $endpoint, ['json' => $data]);
    }

    /**
     * Update a Media Manager object with arbitrary attributes.
     *
     * @param string $type
     * @param string $id
     * @param array $attributes
     */
    public function updateObject(string $type, string $id, array $attributes = []): void
    {
        // Convert common `premiered_on` and `encored_on` fields if provided and
        // implementing DateTimeInterface.
        foreach (['premiered_on', 'encored_on'] as $field) {
            if (isset($attributes[$field]) && $attributes[$field] instanceof DateTimeInterface) {
                $attributes[$field] = $attributes[$field]->format('Y-m-d');
            }
        }

        $this->patch(
            "{$type}s/$id/",
            ['data' => ['type' => $type, 'id' => $id, 'attributes' => $attributes]]
        );
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Update+Episode
     *
     * @param string $id
     * @param array $attributes
     *
     * @see \OpenPublicMedia\PbsMediaManager\Client::addEpisode()
     */
    public function updateEpisode(string $id, array $attributes): void
    {
        $this->updateObject('episode', $id, $attributes);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Update+Episode#UpdateEpisode-Movingepisodes
     *
     * @param string $id
     * @param string $to_type
     * @param string $to_id
     */
    public function moveEpisode(string $id, string $to_type, string $to_id): void
    {
        $this->updateEpisode($id, [$to_type => $to_id]);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Update+Special
     *
     * @param string $id
     * @param array $attributes
     *
     * @see \OpenPublicMedia\PbsMediaManager\Client::addSpecial()
     */
    public function updateSpecial(string $id, array $attributes): void
    {
        $this->updateObject('special', $id, $attributes);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Update+Special#UpdateSpecial-Movingspecials
     *
     * @param string $id
     * @param string $to_type
     * @param string $to_id
     */
    public function moveSpecial(string $id, string $to_type, string $to_id): void
    {
        $this->updateSpecial($id, [$to_type => $to_id]);
    }

    /*
     * DELETE methods.
     */

    /**
     * Sends a DELETE API request.
     *
     * @param string $endpoint
     *   URL to send the DELETE request to.
     */
    public function delete(string $endpoint): void
    {
        $this->request('delete', $endpoint);
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Delete+Episode
     *
     * @param $id
     */
    public function deleteEpisode($id): void
    {
        $this->delete("episodes/$id/");
    }

    /**
     * @url https://docs.pbs.org/display/CDA/Delete+Special
     *
     * @param $id
     */
    public function deleteSpecial($id): void
    {
        $this->delete("specials/$id/");
    }
}
