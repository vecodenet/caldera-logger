<?php

declare(strict_types = 1);

/**
 * Caldera Logger
 * Logger implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2022 Vecode. All rights reserved
 */

namespace Caldera\Tests\Logger;

use Exception;
use RuntimeException;

use PHPUnit\Framework\TestCase;

use Psr\Log\LogLevel;

use Caldera\Logger\Logger;
use Caldera\Logger\Adapter\ClefAdapter;

class LoggerWithClefAdapterTest extends TestCase {

	/**
	 * Log path
	 * @var string
	 */
	protected static $path;

	/**
	 * Adapter instance
	 * @var ClefAdapter
	 */
	protected static $adapter;

	/**
	 * Logger instance
	 * @var Logger
	 */
	protected static $logger;

	protected function setUp(): void {
		# Prepare output file
		self::$path = dirname(__DIR__) . '/output/logger_test.json';
		# Create adapter
		self::$adapter = new ClefAdapter(self::$path);
		self::$adapter->setLevel(LogLevel::DEBUG);
		# Create logger
		self::$logger = new Logger(self::$adapter);
	}

	protected static function truncateLog() {
		if ( file_exists(self::$path) ) {
			# Truncate file
			$stream = @fopen(self::$path, 'w');
			fclose($stream);
		}
	}

	public function testLog() {
		self::truncateLog();
		# Try to log with the file adapter attached
		self::$logger->debug('Already on line 65');
		self::$logger->info('This is an informational message');
		self::$logger->warning('Something is not OK');
		self::$logger->error('An unknown error ocurred');
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertJson($line);
		} else {
			$this->fail('Failed to open log file');
		}
	}

	public function testLogException() {
		self::truncateLog();
		# Try to log with the file adapter attached
		try {
			throw new RuntimeException('Something has gone haywire', 100);
		} catch (Exception $e) {
			self::$logger->critical($e->getMessage(), ['@x' => $e, '@i' => $e->getCode()]);
		}
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertJson($line);
		} else {
			$this->fail('Failed to open log file');
		}
	}

	public function testLogTemplate() {
		self::truncateLog();
		# Try to log with the file adapter attached
		$message = 'Booting machine: {machine}';
		$machine = gethostname();
		# Turn off automatic interpolation
		self::$logger->autoInterpolate(false);
		# Log something passing a context variable
		self::$logger->info($message, ['machine' => $machine]);
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertJson($line);
		} else {
			$this->fail('Failed to open log file');
		}
	}
}
