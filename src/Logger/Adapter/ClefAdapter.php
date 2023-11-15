<?php

declare(strict_types = 1);

/**
 * Caldera Logger
 * Logger implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2022 Vecode. All rights reserved
 */

namespace Caldera\Logger\Adapter;

use Throwable;

use Psr\Log\LogLevel;

use Caldera\Logger\Adapter\AbstractAdapter;

class ClefAdapter extends AbstractAdapter {

	/**
	 * Path to the log file
	 */
	protected string $path = '';

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
	 * Logs with an arbitrary level
	 * @param  mixed  $level   Log level
	 * @param  string $message Message to log
	 */
	public function log($level, string $message, array $context = []): void {
		$normalized_level = 'Information';
		switch ($level) {
			case 'debug':
				$normalized_level = 'Verbose';
			break;
			case 'info':
			case 'notice':
				$normalized_level = 'Information';
			break;
			case 'warning':
				$normalized_level = 'Warning';
			break;
			case 'error':
			case 'alert':
				$normalized_level = 'Error';
			break;
			case 'emergency':
			case 'critical':
				$normalized_level = 'Fatal';
			break;
		}
		$event = [
			'@t' => date('c'),
			'@mt' => $message,
			'@l' => $normalized_level,
		];
		foreach ($context as $key => $value) {
			switch ($key) {
				case '@x':
					if ($value instanceof Throwable) {
						$event['@x'] = $value->getMessage() . PHP_EOL . $value->getTraceAsString();
					}
				break;
				case '@i':
					$event['@i'] = $value;
				break;
				default:
					$event[$key] = $value;
			}
		}
		$output = json_encode($event);
		$file = fopen($this->path, 'a');
		if ($file) {
			fwrite($file, trim($output) . PHP_EOL);
			fclose($file);
		}
	}
}
