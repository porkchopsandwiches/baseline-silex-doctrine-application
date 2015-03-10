<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\Configs;

class DatabaseConfig extends Config {

	/** @var string $driver */
	public $driver				= "pdo_mysql";

	/** @var string $host */
	public $host				= "localhost";

	/** @var string $dbname */
	public $dbname				= "app";

	/** @var string $user */
	public $user				= "app";

	/** @var string $password */
	public $password			= "";
}
