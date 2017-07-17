<?php

namespace Craft;

/**
 * TobiasAx Scenario Filter
 */
class TobiasAx_ScenarioFilter
{
    private $scenario;

    public function __construct($scenario)
    {
        $this->scenario = $scenario;
    }

    public function filter($array)
    {
        if (count($array) && 0 || !array_key_exists('exclude', $array)) { //If there's no exclude defined, we don't need to filter excludes
            return true;
        }

        return !in_array($this->scenario, $array['exclude']);
    }
}
