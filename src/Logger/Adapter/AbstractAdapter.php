<?php

declare(strict_types = 1);

/**
 * Caldera Logger
 * Logger implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2022 Vecode. All rights reserved
 */

namespace Caldera\Logger\Adapter;

use Psr\Log\LogLevel;

abstract class AbstractAdapter implements AdapterInterface {

	/**
	 * Log level
	 * @var mixed
	 */
	protected $level = LogLevel::DEBUG;

	/**
	 * Set log level
	 * @param  mixed $level Log level
	 * @return $this
	 */
	public function setLevel($level) {
		$this->level = $level;
		return $this;
	}

	/**
	 * Get log level
	 * @return mixed Log level
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * Logs with an arbitrary level
	 * @param  mixed  $level   Log level
	 * @param  string $message Message to log
	 */
	abstract public function log($level, string $message): void;
}
