<?php

require_once 'vendor/autoload.php';
require_once 'src/FootballOracle/Oracle.php';

function match($c1, $c2) {
    try {
        return Oracle::match($c1, $c2);
    } catch (FootballOracleException $e) {
        echo "<pre>";
        print_r($e);
        echo "</pre>";
    }
}

echo "<pre>";
print_r(match(0,19));
echo "</pre>";