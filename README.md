# PBS Media Manager PHP Library

This library abstracts interactions with the 
[PBS Media Manager API](https://docs.pbs.org/display/MM/Media+Manager) based on
the Media Manager Core Data Model:


![PBS Media Manager Core Data Model](pbs-media-manager-data-model.jpg)


## Installation

Install via composer:

```bash
composer require openpublicmedia/pbs-media-manager-php
```

## Use

The primary class provided by this library is the 
`OpenPublicMedia\PbsMediaManager\Client`. A `Client` instance can be used to 
query the API in various ways based on the Core Data Model. The client requires
an API key and secret, provided by PBS.

### Response data structures

Responses from the `Client` class will return either a Generator in the case of 
plural getters (e.g. `getFranchises`, `getShows`, `getSeasons`, etc.) or an
object for singular getters (e.g. `getEpisode`, `getCollection`, etc.). In both
cases, objects representing API data follow a standard structure with the
following properties:

* `id`: guid for the object.
* `type`: string of the object type (e.g. "franchise", "show", "season", etc.).
* `links`: links to related API endpoints for the object.
* `attributes`: metadata about the object.

### Examples

#### Creating a client

```php
use OpenPublicMedia\PbsMediaManager\Client;

$api_key = 'xxxxxxxxxxxxxx'
$api_secret = 'xxxxxxxxxxx'

$client = new Client($api_key, $api_secret);
```

#### Getting all Franchises

```php
$franchises = $client->getFranchises();

foreach ($franchises as $franchise) {
    var_dump($franchise);
    class stdClass#45 (4) {
        public $links => class stdClass#40 (4) { ... }
        public $attributes => class stdClass#39 (18) { ... }
        public $type => string(9) "franchise"
        public $id => string(36) "e08bf78d-e6a3-44b9-b356-8753d01c7327"
      }
}
```

#### Getting a single Episode

```php
$episode = $client->getEpisode('08e7ee9c-800a-406f-86f0-bf0bb77fe42b');

var_dump($episode);
class stdClass#80 (3) {
    public $attributes => class stdClass#38 (20) { ... }
    public $id => string(36) "08e7ee9c-800a-406f-86f0-bf0bb77fe42b"
    public $type => string(7) "episode"
}
```

## Development goals

See [CONTRIBUTING](CONTRIBUTING.md) for information about contributing to
this project.

### v1

 - [x] API authentication (`OpenPublicMedia\PbsMediaManager\Client`)
 - [x] API direct querying (`$client->request()`)
 - [x] Result/error handling
 - [x] GET wrappers for core data objects (`$client->getXXX()`)
 - [x] Transparent paged response handling (`OpenPublicMedia\PbsMediaManager\Response\PagesResponse`)

### v1.x

 - [ ] PUT/PATCH/POST support on relevant endpoints
 - [ ] DELETE support on relevant endpoints
 
### v2.x

 - [ ] Entities for core data objects
 - [ ] Advanced Asset availability handling
 - [ ] Advanced Changelog endpoint operations
