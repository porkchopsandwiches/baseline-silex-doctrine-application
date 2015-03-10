<?php

namespace PorkChopSandwiches\Silex\Doctrine\Baseline\Configs;

use ArrayAccess; # Values can be accessed like an array
use IteratorAggregate; # Can be iterated via foreach()
use ArrayIterator;
use PorkChopSandwiches\Preserialiser\Preserialisable;

abstract class Config implements ArrayAccess, IteratorAggregate, Preserialisable {

	# -----------------------------------------------------
	# ArrayAccess
	# -----------------------------------------------------

	/**
	 * @param string $offset
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function offsetSet($offset, $value) {
		$this -> $offset = $value;
	}

	/**
	 * @param string $offset
	 *
	 * @return $this
	 */
	public function offsetUnset($offset) {
		$this -> $offset = null;
	}

	/**
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return !is_null($this -> $offset);
	}

	/**
	 * @param string $offset
	 *
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this -> $offset;
	}

	# -----------------------------------------------------
	# IteratorAggregate
	# -----------------------------------------------------

	/**
	 * @return ArrayIterator
	 */
	public function getIterator () {
		return new ArrayIterator(get_object_vars($this));
	}

	# -----------------------------------------------------
	# Preserialisation
	# -----------------------------------------------------

	public function preserialise (array $args = array()) {
		return get_object_vars($this);
	}
}
