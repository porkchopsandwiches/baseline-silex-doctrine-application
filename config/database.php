<?php
# CREATE DATABASE insurgent_ufa;
# GRANT ALL PRIVILEGES ON insurgent_ufa.* TO 'insurgent_ufa'@'localhost' IDENTIFIED BY '70uxf7UMVVJtGfxI';

use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\DatabaseConfig;

$config = new DatabaseConfig();

$config -> dbname = "insurgent_ufa";
$config -> user = "insurgent_ufa";
$config -> password = "70uxf7UMVVJtGfxI";

return $config;
