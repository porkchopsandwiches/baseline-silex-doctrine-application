<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\DBAL\Logging\DebugStack;
use PorkChopSandwiches\Doctrine\Utilities\EntityManager\Generator;
use PorkChopSandwiches\Silex\Baseline\Application as BaselineApplication;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Cache\Cache;
use Memcached;
use Doctrine\Common\Annotations\Reader;
use PorkChopSandwiches\Doctrine\Utilities\DB;
use Doctrine\ORM\EntityManager;
use PorkChopSandwiches\Silex\Doctrine\Baseline\ConfigLoaders\ConfigLoaderInterface;
use PorkChopSandwiches\Silex\Baseline\ConfigLoaders\ConfigLoaderInterface as BaselineConfigLoaderInterface;
use Exception;

class Application extends BaselineApplication {

	/** @var ConfigLoaderInterface */
	protected $config_loader;

	/**
	 * @param BaselineConfigLoaderInterface $config_loader
	 *
	 * @throws Exception
	 *
	 * @return $this
	 */
	public function setConfigLoader (BaselineConfigLoaderInterface $config_loader) {

		if (!$config_loader instanceof ConfigLoaderInterface) {
			throw new Exception("Config Loader must be an instance of " . __NAMESPACE__ . "\\ConfigLoaders\\ConfigLoaderInterface");
		}

		$this -> config_loader = $config_loader;
		return $this;
	}

	# -----------------------------------------------------
	# Accessor methods
	# -----------------------------------------------------

	/**
	 * @return Cache
	 */
	static public function getDoctrineCacheDriver () {
		return self::$app["app.doctrine.cache.driver"];
	}

	/**
	 * @return Configuration
	 */
	static public function getDBConfig () {
		return self::$app["db.config"];
	}

	/**
	 * @return Connection
	 */
	static public function getDBConnection () {
		return self::$app["db"];
	}

	/**
	 * @return Memcached
	 */
	static public function getMemcached () {
		return self::$app["app.memcached"];
	}

	/**
	 * @return EntityManager
	 */
	static public function getEM () {
		return self::$app["app.doctrine.entitymanager"];
	}

	/**
	 * @return DB
	 */
	static public function getDBUtilities () {
		return self::$app["app.db"];
	}

	# -----------------------------------------------------
	# Configuration
	# -----------------------------------------------------

	/**
	 * @return array
	 */
	protected function getBaselineConfig () {
		return self::getArraysService() -> deepMerge(parent::getBaselineConfig(), array(
			"environment" => array(
				"root_path"			=> "",
			),
			"doctrine" => array(
				"use_memcache"			=> true,
				"proxies"				=> array(
					"namespace"				=> "App\\Doctrine\\Proxies",
					"directory"				=> "/App/Doctrine/Proxies",
					"autogenerate_strategy"	=> AbstractProxyFactory::AUTOGENERATE_NEVER
				),
				"ensure_production_settings"	=> true
			)
		));
	}

	# -----------------------------------------------------
	# Session
	# -----------------------------------------------------

	protected function configureSession () {
		parent::configureSession();

		$this["session.storage.handler"] = $this -> share(function () {
			return new PdoSessionHandler(
				self::getDBConnection() -> getWrappedConnection(),
				$this -> config_loader -> getSessionConfig() -> preserialise()
			);
		});
	}

	# -----------------------------------------------------
	# Logging
	# -----------------------------------------------------

	/**
	 * Register and configure the MonologServiceProvider, and log database queries.
	 */
	protected function configureLogging () {
		parent::configureLogging();

		$logger = new DebugStack();
		$this -> getDBConfig() -> setSQLLogger($logger);

		$this -> after(function () use ($logger) {
			foreach ($logger -> queries as $query) {
				self::getMonolog() -> debug($query["sql"], array(
					"params" => $query["params"],
					"types" => $query["types"]
				));
			}
		});
	}

	# -----------------------------------------------------
	# Memcached
	# -----------------------------------------------------

	protected function bootstrapMemcached () {
		$this["app.memcached"] = $this -> share(function () {
			$memcached = new Memcached();
			$config = $this -> config_loader -> getMemcachedConfig();
			$memcached -> addServer($config -> host, $config -> port);
			return $memcached;
		});
	}

	# -----------------------------------------------------
	# Doctrine
	# -----------------------------------------------------

	/**
	 * Return the instance of the Doctrine Cache Driver to use.
	 *
	 * @return Cache
	 */
	protected function instantiateDoctrineCacheDriver () {
		if ($this["app.config"]["doctrine.use_memcache"]) {
			$cache_driver = new MemcachedCache();
			$cache_driver -> setMemcached($this -> getMemcached());
		} else {
			$cache_driver = new ArrayCache();
		}

		return $cache_driver;
	}

	/**
	 * Return the instance of the Reader to use for reading Doctrine Annotations.
	 *
	 * @return Reader
	 */
	protected function instantiateDoctrineAnnotationReader () {
		return new CachedReader(new AnnotationReader(), $this -> getDoctrineCacheDriver());
	}

	/**
	 * Registers the Doctrine provider (`db`), and sets up the caching (in `app.doctrine.cache.driver`) and Entity Manager (in `app.doctrine.entitymanager`).
	 */
	protected function bootstrapDoctrine () {
		$this -> register(new DoctrineServiceProvider(), array(
			"db.options" => $this -> config_loader -> getDatabaseConfig() -> preserialise()
		));

		$this["app.doctrine.cache.driver"] = $this -> share(function () {
			return $this -> instantiateDoctrineCacheDriver();
		});

		$this["app.doctrine.entitymanager"] = $this -> share(function () {
			return Generator::manufacture(
				self::getDBConnection(),
				self::getDoctrineCacheDriver(),
				$this -> instantiateDoctrineAnnotationReader(),
				$this -> config_loader -> getEntityPaths(),
				$this["app.config"]["doctrine.proxies.autogenerate_strategy"],
				$this["app.config"]["doctrine.ensure_production_settings"],
				$this["app.config"]["environment.root_path"] . Generator::DOCTRINE_ANNOTATIONS_FILE_PATH,
				$this["app.config"]["doctrine.proxies.namespace"],
				$this["app.config"]["environment.root_path"] . $this["app.config"]["doctrine.proxies.directory"]
			);
		});
	}

	# -----------------------------------------------------
	# Booting
	# -----------------------------------------------------

	/**
	 * Prepare the Application for run() use by configuring and registering Providers.
	 */
	public function bootstrap () {
		$this -> bootstrapInternalServices();
		$this -> bootstrapConfig();
		$this -> bootstrapEnvironment();
		$this -> bootstrapMemcached();
		$this -> bootstrapDoctrine();
		$this -> bootstrapLogging();
		$this -> bootstrapSession();
		$this -> bootstrapTwig();
	}
}
