<?php


namespace OpenPublicMedia\PbsMediaManager\Test;

use Generator;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use OpenPublicMedia\PbsMediaManager\Client;
use OpenPublicMedia\PbsMediaManager\Query\Results;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use stdClass;

class TestCaseBase extends TestCase
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
            Client::STAGING,
            ['handler' => $this->mockHandler]
        );
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object
     *   Instantiated object that we will run method on.
     * @param string $methodName
     *   Method name to call
     * @param array $parameters
     *   Array of parameters to pass into method.
     *
     * @return mixed
     *   Method return.
     *
     * @throws ReflectionException
     *
     * @url https://jtreminio.com/blog/unit-testing-tutorial-part-iii-testing-protected-private-methods-coverage-reports-and-crap/#targeting-private-protected-methods-directly
     */
    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param string $name
     *   Base file name for a JSON fixture file.
     *
     * @return Response
     *   Guzzle 200 response with JSON body content.
     */
    protected static function jsonFixtureResponse(string $name): Response
    {
        return self::apiJsonResponse(
            200,
            file_get_contents(__DIR__ . '/fixtures/' . $name . '.json')
        );
    }

    protected static function apiJsonResponse(int $code, string $json = '[]'): Response
    {
        return new Response($code, ['Content-Type' => 'application/json'], $json);
    }

    /**
     * Gets and verify contents of a Results Generator.
     *
     * @param Results $result
     *   Results from an API query.
     * @param string $type
     *   Expected type of the first Result object.
     */
    protected function verifyGenerator(Results $result, string $type): void
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
     * @param stdClass $result
     *   Results from an API query.
     * @param string $id
     *   Expected GUID of the object.
     * @param string $type
     *   Expected type of the object.
     */
    protected function verifryObject(stdClass $result, string $id, string $type): void
    {
        $this->assertIsObject($result);
        $this->assertObjectHasAttribute('id', $result);
        $this->assertEquals($id, $result->id);
        $this->assertObjectHasAttribute('attributes', $result);
        $this->assertIsObject($result->attributes);
        $this->assertObjectHasAttribute('type', $result);
        $this->assertEquals($type, $result->type);
    }
}
