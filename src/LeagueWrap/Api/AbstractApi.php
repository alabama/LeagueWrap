<?php

namespace LeagueWrap\Api;

use GuzzleHttp\Promise\Promise;
use LeagueWrap\Api;
use LeagueWrap\AsyncClientInterface;
use LeagueWrap\Cache;
use LeagueWrap\CacheInterface;
use LeagueWrap\ClientInterface;
use LeagueWrap\Dto\AbstractDto;
use LeagueWrap\Dto\Summoner;
use LeagueWrap\Exception\CacheNotFoundException;
use LeagueWrap\Exception\InvalidIdentityException;
use LeagueWrap\Exception\LimitReachedException;
use LeagueWrap\Exception\RegionException;
use LeagueWrap\Exception\VersionException;
use LeagueWrap\Limit\Collection;
use LeagueWrap\Region;
use LeagueWrap\Response;
use LeagueWrap\Response\HttpClientError;
use LeagueWrap\Response\HttpServerError;
use LeagueWrap\Response\ResponseException;

abstract class AbstractApi
{
    // TODO add api tests for correct domain

    use ConfigTrait;

    /**
     * The client used to communicate with the api.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * The collection of limits to be used on this api.
     *
     * @var Collection
     */
    protected $collection;

    /**
     * The key to be used by the api.
     *
     * @param string
     */
    protected $key;

    /**
     * Provides access to the api object to perform requests on
     * different api endpoints.
     */
    protected $api;

    /**
     * A list of all permitted regions for this API call. Leave
     * it empty to not lock out any region string.
     *
     * @param array
     */
    protected $permittedRegions = [];

    /**
     * List of http error response codes and associated erro
     * message with each code.
     *
     * @param array
     */
    protected $responseErrors = [
        '400' => 'Bad request.',
        '401' => 'Unauthorized.',
        '403' => 'Forbidden.',
        '404' => 'Resource not found.',
        '429' => 'Rate limit exceeded.',
        '500' => 'Internal server error.',
        '503' => 'Service unavailable.',
    ];

    /**
     * The version we want to use. If null use the first
     * version in the array.
     *
     * @param string|null
     */
    protected $version = null;

    /**
     * A count of the amount of API request this object has done
     * so far.
     *
     * @param int
     */
    protected $requests = 0;

    /**
     * This is the cache container that we intend to use.
     *
     * @var CacheInterface
     */
    protected $cache = null;

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 0;

    /**
     * The amount of seconds to keep things in cache.
     *
     * @var int
     */
    protected $seconds = 0;

    /**
     * Default DI constructor.
     *
     * @param ClientInterface $client
     * @param Collection      $collection
     * @param Api             $api
     */
    public function __construct(ClientInterface $client, Collection $collection, Api $api)
    {
        $this->client = $client;
        $this->collection = $collection;
        $this->api = $api;
    }

    /**
     * Returns the amount of requests this object has done
     * to the api so far.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requests;
    }

    /**
     * Set the key to be used in the api.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Select the version of the api you wish to
     * query.
     *
     * @param string $version
     *
     * @throws VersionException
     *
     * @return $this
     */
    public function selectVersion($version)
    {
        if (!in_array($version, $this->versions)) {
            throw new VersionException('Invalid version selected');
        }

        $this->version = $version;

        return $this;
    }

    /**
     * @return Region region of this api
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string domain used for the request
     */
    abstract public function getDomain();

    /**
     * Sets the amount of seconds we should remember the response for.
     * Leave it empty (or null) if you want to use the default set for
     * each api request.
     *
     * @param int            $seconds
     * @param CacheInterface $cache
     *
     * @return $this
     */
    public function remember($seconds = null, CacheInterface $cache = null)
    {
        if (is_null($cache)) {
            // use the built in cache interface
            $cache = new Cache();
        }
        $this->cache = $cache;
        if (is_null($seconds)) {
            $this->seconds = $this->defaultRemember;
        } else {
            $this->seconds = $seconds;
        }

        return $this;
    }

    protected function requestAsync($path, $params = [], $static = false, $isVersioned = true)
    {
        if (!$this->client instanceof AsyncClientInterface) {
            // @TODO: Refactor into an option on the Api class
            throw new \LogicException('Cannot use async request without async client');
        }

        $this->preRequestSetup();

        // check if we have hit the limit
        if (!$static && !$this->collection->hitLimits($this->region->getRegion())) {
            throw new LimitReachedException('You have hit the request limit in your collection.');
        }


        return $this->handleResponseCaching(function ($static, $uri, $params, $cacheKey) {
            ++$this->requests;

            return $this->client->requestAsync($uri, $params)->then(function (Response $response) use ($cacheKey) {
                $this->checkResponseErrors($response);

                if ($cacheKey) {
                    $this->cache->set($cacheKey, $response, $this->seconds);
                }

                return $response;
            });
        }, $path, $params, $static, $isVersioned)->then(function ($response) {
            return json_decode($response, true);
        });
    }

    /**
     * Wraps the request of the api in this method.
     *
     * @param string $path
     * @param array  $params
     * @param bool   $static
     * @param bool   $isVersioned
     *
     * @throws CacheNotFoundException
     * @throws HttpClientError
     * @throws HttpServerError
     * @throws LimitReachedException
     * @throws RegionException
     *
     * @return mixed
     */
    protected function request($path, $params = [], $static = false, $isVersioned = true)
    {
        $this->preRequestSetup();

        $content = $this->handleResponseCaching(function ($static, $uri, $params) {
            return $this->clientRequest($static, $uri, $params);
        }, $path, $params, $static, $isVersioned);

        // decode the content
        return json_decode($content, true);
    }

    /**
     * Method that wraps the caching logic and enables the usage of sync or async client.
     *
     * @param callable $requestFunction
     * @param          $path
     * @param          $params
     * @param          $static
     * @param          $isVersioned
     * @param bool     $returnPromise
     *
     * @throws CacheNotFoundException
     * @throws HttpClientError
     * @throws HttpServerError
     *
     * @return null|string|Promise
     */
    protected function handleResponseCaching(
        callable $requestFunction, $path, $params, $static, $isVersioned, $returnPromise = false
    ) {
        // add the key to the param list
        $params['api_key'] = $this->key;

        $uri = ($isVersioned) ? $this->getVersion().'/'.$path : $path;

        $content = null;

        // check cache
        if ($this->cache instanceof CacheInterface) {
            $cacheKey = md5($this->getDomain().$uri.'?'.http_build_query($params));
            if ($this->cache->has($cacheKey)) {
                $content = $this->cache->get($cacheKey);
                if ($content instanceof HttpClientError ||
                    $content instanceof HttpServerError
                ) {
                    // this was a cached client error... throw it
                    throw $content;
                }
            } elseif ($this->cacheOnly) {
                throw new CacheNotFoundException("A cache item for '$uri?".http_build_query($params)."' was not found!");
            } else {
                try {
                    if (!$returnPromise) {
                        $content = $requestFunction($static, $uri, $params, $cacheKey);
                        // we want to cache this response
                        $this->cache->set($cacheKey, $content, $this->seconds);
                    } else {
                        return $requestFunction($static, $uri, $params, $cacheKey);
                    }
                } catch (HttpClientError $clientError) {
                    if ($this->cacheClientError) {
                        // cache client errors
                        $this->cache->set($cacheKey, $clientError, $this->seconds);
                    }
                    // rethrow the exception
                    throw $clientError;
                } catch (HttpServerError $serverError) {
                    if ($this->cacheServerError) {
                        // cache server errors
                        $this->cache->set($cacheKey, $serverError, $this->seconds);
                    }
                    // rethrow the exception
                    throw $serverError;
                }
            }
        } elseif ($this->cacheOnly) {
            throw new CacheNotFoundException('The cache is not enabled but we were told to use only the cache!');
        } else {
            if (!$returnPromise) {
                $content = $requestFunction($static, $uri, $params, false);
            } else {
                return $requestFunction($static, $uri, $params, false);
            }
        }

        return $content;
    }

    /**
     * Make the actual request.
     *
     * @param bool   $static
     * @param string $uri
     * @param array  $params
     *
     * @throws LimitReachedException
     *
     * @return \LeagueWrap\Response
     */
    protected function clientRequest($static, $uri, $params)
    {
        // check if we have hit the limit
        if (!$static &&
            !$this->collection->hitLimits($this->region->getRegion())
        ) {
            throw new LimitReachedException('You have hit the request limit in your collection.');
        }

        $response = $this->client->request($uri, $params);
        ++$this->requests;
        // check if it's a valid response object
        if ($response instanceof Response) {
            $this->checkResponseErrors($response);
        }

        return $response;
    }

    /**
     * Get the version string.
     *
     * @return string
     */
    public function getVersion()
    {
        if (is_null($this->version)) {
            // get the first version in versions
            $this->version = reset($this->versions);
        }

        return $this->version;
    }

    /**
     * Attempts to extract an ID from the object/value given.
     *
     * @param Summoner|int $identity
     *
     * @throws InvalidIdentityException
     *
     * @return int
     */
    protected function extractId($identity)
    {
        if ($identity instanceof Summoner) {
            return $identity->id;
        } elseif (filter_var($identity, FILTER_VALIDATE_INT) !== false) {
            return $identity;
        } else {
            throw new InvalidIdentityException("The identity '$identity' is not valid.");
        }
    }

    /**
     * Attempts to extract an ID from the array given.
     *
     * @param mixed $identities
     *
     * @return array
     *
     * @uses extractId()
     */
    protected function extractIds($identities)
    {
        $ids = [];
        if (is_array($identities)) {
            foreach ($identities as $identity) {
                $ids[] = $this->extractId($identity);
            }
        } else {
            $ids[] = $this->extractId($identities);
        }

        return $ids;
    }

    /**
     * Attempts to attach the response to a summoner object.
     *
     * @param mixed  $identity
     * @param mixed  $response
     * @param string $key
     *
     * @return bool
     */
    protected function attachResponse($identity, $response, $key)
    {
        if ($identity instanceof Summoner) {
            $identity->set($key, $response);

            return true;
        }

        return false;
    }

    /**
     * Attempts to attach all the responses to the correct summoner.
     *
     * @param array|Summoner $identities
     * @param mixed          $responses
     * @param string         $key
     *
     * @return bool
     */
    protected function attachResponses($identities, $responses, $key)
    {
        if (is_array($identities)) {
            foreach ($identities as $identity) {
                if ($identity instanceof Summoner) {
                    $id = $identity->id;
                    if (isset($responses[$id])) {
                        $response = $responses[$id];
                        $this->attachResponse($identity, $response, $key);
                    } else {
                        // we did not get a response for this id, attach null
                        $this->attachResponse($identity, null, $key);
                    }
                }
            }
        } else {
            $identity = $identities;
            if ($identity instanceof Summoner) {
                $id = $identity->id;
                if (isset($responses[$id])) {
                    $response = $responses[$id];
                    $this->attachResponse($identity, $response, $key);
                } else {
                    // we did not get a response for this id, attach null
                    $this->attachResponse($identity, null, $key);
                }
            }
        }

        return true;
    }

    /**
     * Will attempt to attach any static data to the given dto if
     * the attach static data flag is set.
     *
     * @param AbstractDto $dto
     *
     * @return AbstractDto
     */
    protected function attachStaticDataToDto(AbstractDto $dto)
    {
        if ($this->attachStaticData) {
            $dto->loadStaticData($this->staticData);
        }

        return $dto;
    }

    /**
     * Checks the response for Http errors.
     *
     * @param Response $response
     *
     * @throws \LeagueWrap\Response\Http400
     * @throws \LeagueWrap\Response\Http401
     * @throws \LeagueWrap\Response\Http402
     * @throws \LeagueWrap\Response\Http403
     * @throws \LeagueWrap\Response\Http404
     * @throws \LeagueWrap\Response\Http405
     * @throws \LeagueWrap\Response\Http406
     * @throws \LeagueWrap\Response\Http407
     * @throws \LeagueWrap\Response\Http408
     * @throws \LeagueWrap\Response\Http429
     * @throws \LeagueWrap\Response\Http500
     * @throws \LeagueWrap\Response\Http501
     * @throws \LeagueWrap\Response\Http502
     * @throws \LeagueWrap\Response\Http503
     * @throws \LeagueWrap\Response\Http504
     * @throws \LeagueWrap\Response\Http505
     * @throws \LeagueWrap\Response\UnderlyingServiceRateLimitReached
     */
    protected function checkResponseErrors(Response $response)
    {
        $code = $response->getCode();
        if ($code === 429 && !$response->hasHeader('Retry-After')) {
            throw Response\UnderlyingServiceRateLimitReached::withResponse(
                "Did not receive 'X-Rate-Limit-Type' and 'Retry-After' headers. ".
                'See https://developer.riotgames.com/docs/rate-limiting for more details',
                $response
            );
        }
        if (intval($code / 100) != 2) {
            // we have an error!
            $message = 'Http Error.';
            if (isset($this->responseErrors[$code])) {
                $message = trim($this->responseErrors[$code]);
            }

            $class = 'LeagueWrap\Response\Http'.$code;

            if (class_exists($class) && is_subclass_of($class, ResponseException::class)) {
                throw $class::withResponse($message, $response);
            }
        }
    }

    protected function preRequestSetup()
    {
        // get and validate the region
        if ($this->region->isLocked($this->permittedRegions)) {
            throw new RegionException('The region "'.$this->region->getRegion().'" is not permitted to query this API.');
        }

        $this->client->baseUrl($this->getDomain());

        if ($this->timeout > 0) {
            $this->client->setTimeout($this->timeout);
        }
    }
}
