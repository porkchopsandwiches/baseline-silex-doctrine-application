<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\Configs;

class MemcachedConfig extends Config {

	/** @var string $host */
	public $host				= "localhost";

	/** @var int $port */
	public $port				= 11211;
}
