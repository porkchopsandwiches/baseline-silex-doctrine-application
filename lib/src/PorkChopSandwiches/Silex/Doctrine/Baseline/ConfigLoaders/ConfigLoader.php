<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\ConfigLoaders;

use PorkChopSandwiches\Silex\Baseline\ConfigLoaders\ConfigLoader as BaselineConfigLoader;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\SessionConfig;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\MemcachedConfig;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\DatabaseConfig;

class ConfigLoader extends BaselineConfigLoader implements ConfigLoaderInterface {

	/**
	 * @return SessionConfig
	 */
	public function getSessionConfig () {
		return new SessionConfig();
	}

	/**
	 * @return MemcachedConfig
	 */
	public function getMemcachedConfig () {
		return new MemcachedConfig();
	}

	/**
	 * @return DatabaseConfig
	 */
	public function getDatabaseConfig () {
		return new DatabaseConfig();
	}

	/**
	 * @return string[]
	 */
	public function getEntityPaths () {
		return array();
	}
}
