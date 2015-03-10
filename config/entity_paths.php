<?php

$root = dirname(__FILE__);

$paths = array_map(function ($ns) use ($root) {
	return $root . "/../lib/src/Hoodlum/Hopscotch/Insurgent/UFA/" . $ns . "/Entities";
}, array("SharePairs", "FacebookAccounts", "Releases", "Scraper", "Common", "FacebookCache", "Tokens"));

return $paths;
