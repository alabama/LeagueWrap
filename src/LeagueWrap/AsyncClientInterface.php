<?php

namespace LeagueWrap;

use GuzzleHttp\Promise\Promise;

interface AsyncClientInterface
{
    /**
     * @param       $request
     * @param array $params
     *
     * @return Promise
     */
    public function requestAsync($request, array $params = []);
}
