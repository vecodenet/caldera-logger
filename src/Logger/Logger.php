<?php

declare(strict_types = 1);

/**
 * Caldera Logger
 * Logger implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2022 Vecode. All rights reserved
 */

namespace Caldera\Logger;

use DateTimeInterface;
use DateTime;
use Stringable;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

use Caldera\Logger\Adapter\AdapterInterface;

class Logger extends AbstractLogger {

	/**
	 * Array to map log levels to integers
	 * @var array
	 */
	static $levels = [
		'emergency' => 600,
		'alert'     => 550,
		'critical'  => 500,
		'error'     => 400,
		'warning'   => 300,
		'notice'    => 210,
		'info'      => 200,
		'debug'     => 100
	];

	/**
	 * Adapters array
	 * @var array
	 */
	protected $adapters = [];

	/**
	 * Constructor
	 * @param AdapterInterface $adapter AdapterInterface instance
	 */
	public function __construct(AdapterInterface$adapter) {
		$this->adapters[] = $adapter;
	}

	/**
	 * Add a new Adapter
	 * @param  AdapterInterface $adapter Adapter instance
	 * @return $this
	 */
	public function attach(AdapterInterface $adapter) {
		$this->adapters[] = $adapter;
		return $this;
	}

	/**
	 * Logs with an arbitrary level
	 * @param  mixed             $level   Log level
	 * @param  string|Stringable $message Message to log
	 * @param  array             $context Additional context data
	 */
	public function log($level, string|Stringable $message, array $context = []): void {
		$messageLevel = $this->map($level);
		if ( $this->adapters ) {
			$message = $this->interpolate($message, $context);
			foreach ($this->adapters as $adapter) {
				$adapter_level = $this->map( $adapter->getLevel() );
				if ( $adapter_level > $messageLevel ) continue;
				$adapter->log($level, $message, $context);
			}
		}
	}

	/**
	 * Interpolate placeholders
	 * @param  string|Stringable $message Message with placeholders
	 * @param  array             $context Array of context data
	 * @return string
	 */
	protected function interpolate(string|Stringable $message, array $context = []): string {
		$message = strval($message);
		$ret = $message;
		if ( strpos($message, '{') !== false ) {
			$replacements = array();
			foreach ($context as $key => $val) {
				if ( $val === null || is_scalar($val) || ( is_object($val) && method_exists($val, '__toString') ) ) {
					$replacements["{{$key}}"] = $val;
				} elseif ($val instanceof DateTimeInterface) {
					$replacements["{{$key}}"] = $val->format(DateTime::RFC3339);
				} elseif (is_object($val)) {
					$replacements["{{$key}}"] = '[object ' . get_class($val) . ']';
				} else {
					$replacements["{{$key}}"] = '[' . gettype($val) . ']';
				}
			}
			$ret = strtr($message, $replacements);
		}
		return $ret;
	}

	/**
	 * Map a LogLevel to a numeric value
	 * @param  mixed $level Log level
	 * @return int
	 */
	protected function map($level): int {
		$ret = isset( static::$levels[$level] ) ? static::$levels[$level] : 0;
		if ($ret == 0) {
			throw new InvalidArgumentException("Level '{$level}' is not a valid log level");
		}
		return $ret;
	}
}
