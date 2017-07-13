<?php

namespace LeagueWrap\Dto;

class Summoner extends AbstractDto
{
    /**
     * Attempts to get a rune page by the id.
     *
     * @param int $runePageId
     *
     * @return RunePage|null
     */
    public function runePage($runePageId)
    {
        if (!isset($this->info['runePages'])) {
            // no rune pages
            return;
        }
        $runePages = $this->info['runePages'];
        if (isset($runePages[$runePageId])) {
            return $runePages[$runePageId];
        }
    }

    /**
     * Attempts to get the mastery page by the id.
     *
     * @param int $masteryPageId
     *
     * @return MasteryPage|null
     */
    public function masteryPage($masteryPageId)
    {
        if (!isset($this->info['masteryPages'])) {
            // no rune pages
            return;
        }
        $masteryPages = $this->info['masteryPages'];
        if (isset($masteryPages[$masteryPageId])) {
            return $masteryPages[$masteryPageId];
        }
    }

    /**
     * Attempts to get the game by the id.
     *
     * @param int $gameId
     *
     * @return MatchReference|null
     */
    public function recentMatchList($gameId)
    {
        if (!isset($this->info['matchlist'])) {
            // no rune pages
            return;
        }
        $matchlist = $this->info['matchlist'];

        return $matchlist->match($gameId);
    }

    /**
     * Attempts to get a league by the champion/team name or
     * id.
     *
     * @param mixed $identity
     *
     * @return League|null
     */
    public function league($identity, $queue = "RANKED_SOLO_5x5")
    {
        if (!isset($this->info['leagues'])) {
            // no leagues
            return;
        }
        $leagues = $this->info['leagues'];
        foreach ($leagues as $league) {
            //check if the queue is the right one
            if(strtolower($league->queue) != strtolower($queue)) {
                continue;
            }

            if (is_null($league->playerOrTeam)) {
                // we could not find the player or team in this league
                continue;
            }

            // try the name
            if (strtolower($league->playerOrTeam->playerOrTeamName) == strtolower($identity)) {
                return $league;
            }

            // try the id
            if ($league->playerOrTeam->playerOrTeamId == $identity) {
                return $league;
            }
        }
    }
}
