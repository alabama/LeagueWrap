<?php

namespace LeagueWrap\Response;

class Http429 extends HttpClientError
{
    public function isRateExceeded() {
        return $this->hasResponse() && $this->getResponse()->hasHeader("Retry-After");
    }

    /**
     * @return int  The amount of seconds, which should be waited before the next retry
     */
    public function getRetryAfterInSeconds() {
        $waitTimeInSeconds = 0;
        if($this->isRateExceeded()) {
            $waitTimeInSeconds = intval($this->getResponse()->getHeader("Retry-After"));
        }
        return $waitTimeInSeconds;
    }
}
