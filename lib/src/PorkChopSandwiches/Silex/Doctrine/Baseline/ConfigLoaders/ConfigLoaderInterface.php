<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\ConfigLoaders;

use PorkChopSandwiches\Silex\Baseline\ConfigLoaders\ConfigLoaderInterface as BaselineConfigLoaderInterface;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\SessionConfig;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\MemcachedConfig;
use PorkChopSandwiches\Silex\Doctrine\Baseline\Configs\DatabaseConfig;

/**
 * Interface ConfigLoaderInterface
 */
interface ConfigLoaderInterface extends BaselineConfigLoaderInterface {

	/**
	 * @return SessionConfig
	 */
	public function getSessionConfig ();

	/**
	 * @return MemcachedConfig
	 */
	public function getMemcachedConfig ();

	/**
	 * @return DatabaseConfig
	 */
	public function getDatabaseConfig ();

	/**
	 * @return string[]
	 */
	public function getEntityPaths ();
}
