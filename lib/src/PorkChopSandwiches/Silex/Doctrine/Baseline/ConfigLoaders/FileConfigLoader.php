<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\ConfigLoaders;

use PorkChopSandwiches\Silex\Baseline\ConfigLoaders\FileConfigLoader as BaselineFileConfigLoader;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\SessionConfig;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\MemcachedConfig;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\DatabaseConfig;
use Exception;

class FileConfigLoader extends BaselineFileConfigLoader implements ConfigLoaderInterface {

	/**
	 * @throws Exception
	 *
	 * @return SessionConfig
	 */
	public function getSessionConfig () {
		$config = $this -> loadConfigFile("session");

		if (!$config instanceof SessionConfig) {
			throw new Exception("Session Config file must return a SessionConfig instance.");
		}

		return $config;
	}

	/**
	 * @throws Exception
	 *
	 * @return MemcachedConfig
	 */
	public function getMemcachedConfig () {
		$config = $this -> loadConfigFile("memcache");

		if (!$config instanceof MemcachedConfig) {
			throw new Exception("Memcache Config file must return a MemcacheConfig instance.");
		}

		return $config;
	}

	/**
	 * @throws Exception
	 *
	 * @return DatabaseConfig
	 */
	public function getDatabaseConfig () {
		$config = $this -> loadConfigFile("database");

		if (!$config instanceof DatabaseConfig) {
			throw new Exception("Database Config file must return a DatabaseConfig instance.");
		}

		return $config;
	}

	/**
	 * @throws Exception
	 *
	 * @return string[]
	 */
	public function getEntityPaths () {
		$config = $this -> loadConfigFile("entity_paths");

		if (!is_array($config)) {
			throw new Exception("Entity Paths Config file must return an array.");
		}

		return $config;
	}
}
