<?php

require_once 'src/Exceptions/FootballOracleException.php';
require_once 'src/Exceptions/DataException.php';

use Tightenco\Collect;


class Oracle
{
    /**
     * @param int $firstTeamId
     * @param int $secondTeamId
     * @return array
     * @throws FootballOracleException
     */
    public static function match($firstTeamId, $secondTeamId): array
    {
        $firstTeamData = self::getTeamData($firstTeamId);
        $secondTeamData = self::getTeamData($secondTeamId);

        return self::calculate($firstTeamData, $secondTeamData);
    }

    /**
     * @param $teamId
     * @return mixed
     * @throws FootballOracleException
     */
    private static function getTeamData($teamId)
    {
        $teamData = collect(require 'datastorage/teams.php');

        if ($teamData->get($teamId)) {
            return $teamData->get($teamId);
        } else {
            throw new FootballOracleException("Team with id = {$teamId} is not exists in the store");
        }
    }

    /**
     * @param $firstTeamData
     * @param $secondTeamData
     * @return array
     */
    private static function calculate($firstTeamData, $secondTeamData): array
    {
        $teamForces = [
            [
                'force' => self::getTeamForce($firstTeamData),
                'defence' => self::getTeamDefence($firstTeamData),
                'probability' => ((self::getProbability($firstTeamData['games'], $firstTeamData['win']) * self::getProbability($firstTeamData['games'], $firstTeamData['defeat'])) * rand(1, 10)),
            ],
            [
                'force' => self::getTeamForce($secondTeamData),
                'defence' => self::getTeamDefence($secondTeamData),
                'probability' => ((self::getProbability($secondTeamData['games'], $secondTeamData['win']) * self::getProbability($secondTeamData['games'], $secondTeamData['defeat'])) * rand(1, 10)),
            ]
        ];

        return [
            round($teamForces[0]['force'] * $teamForces[0]['force'] * $teamForces[0]['probability'] * $teamForces[1]['defence']),
            round($teamForces[1]['force'] * $teamForces[1]['force'] * $teamForces[1]['probability'] * $teamForces[0]['defence']),
        ];
    }

    /**
     * @param $teamData
     * @return float|int
     */
    private static function getTeamForce($teamData)
    {
        return $teamData['goals']['scored'] / $teamData['games'];
    }

    /**
     * @param $teamData
     * @return float|int
     */
    private static function getTeamDefence($teamData)
    {
        return $teamData['goals']['skiped'] / $teamData['games'];
    }

    /**
     * @param $gamesCount
     * @param $needleCount
     * @return float|int
     */
    private static function getProbability($gamesCount, $needleCount)
    {
        return ($needleCount * 100 / $gamesCount) / 100;
    }
}