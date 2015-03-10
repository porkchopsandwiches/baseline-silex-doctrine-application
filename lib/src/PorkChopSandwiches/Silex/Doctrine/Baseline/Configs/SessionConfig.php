<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\Configs;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SessionConfig extends Config {

	/** @var string $db_table */
	public $db_table			= "sessions";

	/** @var string $db_id_col */
	public $db_id_col			= "id";

	/** @var string $db_data_col */
	public $db_data_col			= "value";

	/** @var string $db_time_col */
	public $db_time_col			= "session_time";

	/** @var string $db_lifetime_col */
	public $db_lifetime_col		= "lifetime";

	/** @var int $lock_mode */
	public $lock_mode			= PdoSessionHandler::LOCK_NONE;
}
