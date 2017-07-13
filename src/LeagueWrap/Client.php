<?php

namespace LeagueWrap;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use LeagueWrap\Exception\BaseUrlException;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface, AsyncClientInterface
{
    protected $guzzle;
    protected $timeout = 0;

    /**
     * Sets the base url to be used for future requests.
     *
     * @param string $url
     *
     * @return void
     */
    public function baseUrl($url)
    {
        $this->guzzle = $this->buildGuzzle($url);
    }

    private function buildGuzzle($url, $handler = null)
    {
        $config = [
            'base_uri' => $url,
            'defaults' => ['headers' => ['Accept-Encoding' => 'gzip,deflate']],
        ];
        if (isset($handler)) {
            $config['handler'] = $handler;
        }

        return new Guzzle($config);
    }

    /**
     * Set a timeout in seconds for how long we will wait for the server
     * to respond. If the server does not respond within the set number
     * of seconds we throw an exception.
     *
     * @param int $seconds
     *
     * @return void
     */
    public function setTimeout($seconds)
    {
        $this->timeout = floatval($seconds);
    }

    /**
     * Attempt to add a mocked handler stack to guzzle, primary usage is
     * to be able to test this code.
     *
     * @param \GuzzleHttp\HandlerStack $mock
     *
     * @return void
     */
    public function addMock($mock)
    {
        // Replace the current guzzle client with the mocked version
        $this->guzzle = $this->buildGuzzle(
            $this->guzzle->getConfig()['base_uri'],
            $mock
        );
    }

    /**
     * Attempts to do a request of the given path.
     *
     * @param string $path
     * @param array  $params
     *
     * @throws BaseUrlException
     *
     * @return \LeagueWrap\Response
     */
    public function request($path, array $params = [])
    {
        if (!$this->guzzle instanceof Guzzle) {
            throw new BaseUrlException('BaseUrl was never set. Please call baseUrl($url).');
        }

        $query = http_build_query($params, null, '&');
        $query = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
        $uri = $path.'?'.$query;
        $response = $this->guzzle
            ->get($uri, ['timeout'     => $this->timeout,
                         'http_errors' => false, ]);
        $body = $response->getBody();
        $code = $response->getStatusCode();
        $headers = $response->getHeaders();
        if ($body instanceof Stream) {
            $body->seek(0);
            $content = ($body->getSize() > 0) ? $body->read($body->getSize()) : null;
        } else {
            // no content
            $content = '';
        }
        $response = new Response($content, $code, $headers);

        return $response;
    }

    public function requestAsync($path, array $params = [])
    {
        if (!$this->guzzle instanceof Guzzle) {
            throw new BaseUrlException('BaseUrl was never set. Please call baseUrl($url) since Client does not exist.');
        }
        $query = http_build_query($params, null, '&');
        $query = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
        $uri = $path.'?'.$query;
        return $this->guzzle->getAsync($uri, [
            'timeout'     => $this->timeout,
            'http_errors' => false,
        ])->then(function (ResponseInterface $response) {
            $wrapResponse = new Response(
                (string) $response->getBody(),
                $response->getStatusCode(),
                $response->getHeaders()
            );
            return $wrapResponse;
        }, function (RequestException $e) {
            return $e;
        });
    }
}
