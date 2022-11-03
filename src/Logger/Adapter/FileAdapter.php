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

use Caldera\Logger\Adapter\AbstractAdapter;

class FileAdapter extends AbstractAdapter {

	/**
	 * Path to the log file
	 * @var string
	 */
	protected $path = '';

	/**
	 * Timestamp format
	 * @var string
	 */
	protected $timestamp = 'Y-m-d H:i:s';

	/**
	 * Log format
	 * @var string
	 */
	protected $format = '[%1$s] %2$s: %3$s';

	/**
	 * Constructor
	 * @param string $path  Path of the log file
	 * @param mixed  $level Log level
	 */
	function __construct(string $path, $level = LogLevel::DEBUG) {
		$this->path = $path;
		$this->level = $level;
	}

	/**
	 * Set the timestamp format
	 * @param  string $timestamp Timestamp format
	 * @return $this
	 */
	public function setTimestamp(string $timestamp) {
		$this->timestamp = $timestamp;
		return $this;
	}

	/**
	 * Set the log format
	 * @param  string $format Log format
	 * @return $this
	 */
	public function setFormat(string $format) {
		$this->format = $format;
		return $this;
	}

	/**
	 * Get the timestamp format
	 * @return string
	 */
	public function getTimestamp(): string {
		return $this->timestamp;
	}

	/**
	 * Get the log format
	 * @return string
	 */
	public function getFormat(): string {
		return $this->format;
	}

	/**
	 * Logs with an arbitrary level
	 * @param  mixed  $level   Log level
	 * @param  string $message Message to log
	 */
	public function log($level, string $message): void {
		$timestamp = date($this->timestamp);
		$format = $this->format ?: $this->format;
		$output = sprintf($format, $timestamp, strtoupper($level), $message);
		$file = fopen($this->path, 'a');
		if ($file) {
			fwrite($file, trim($output) . PHP_EOL);
			fclose($file);
		}
	}
}
