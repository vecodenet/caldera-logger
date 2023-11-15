<?php

declare(strict_types = 1);

/**
 * Caldera Logger
 * Logger implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2022 Vecode. All rights reserved
 */

namespace Caldera\Logger\Adapter;

interface AdapterInterface {

	/**
	 * Logs with an arbitrary level
	 * @param  mixed  $level   Log level
	 * @param  string $message Message to log
	 * @param  array  $context Additional context data
	 */
	function log($level, string $message, array $context = []): void;
}
