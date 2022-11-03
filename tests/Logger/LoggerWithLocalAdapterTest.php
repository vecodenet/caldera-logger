<?php

declare(strict_types = 1);

/**
 * Caldera Logger
 * Logger implementation, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2022 Vecode. All rights reserved
 */

namespace Caldera\Tests\Logger;

use DateTime;
use Exception;

use PHPUnit\Framework\TestCase;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

use Caldera\Logger\Logger;
use Caldera\Logger\Adapter\FileAdapter;

class LoggerWithLocalAdapterTest extends TestCase {

	/**
	 * Log path
	 * @var string
	 */
	protected static $path;

	/**
	 * Adapter instance
	 * @var FileAdapter
	 */
	protected static $adapter;

	/**
	 * Logger instance
	 * @var Logger
	 */
	protected static $logger;

	protected function setUp(): void {
		# Prepare output file
		self::$path = dirname(__DIR__) . '/output/logger_test.log';
		# Create adapter
		self::$adapter = new FileAdapter(self::$path);
		self::$adapter->setLevel(LogLevel::INFO);
		# Create logger
		self::$logger = new Logger();
		self::$logger->attach(self::$adapter);
	}

	protected static function truncateLog() {
		if ( file_exists(self::$path) ) {
			# Truncate file
			$stream = @fopen(self::$path, 'w');
			fclose($stream);
		}
	}

	public function testLogUnknownLevelMustRaiseException() {
		try {
			# Try to log with an invalid level
			self::$logger->log('DUMMY', 'This should raise an error');
			$this->fail('This must throw an InvalidArgumentException');
		} catch (Exception $e) {
			$this->assertInstanceOf(InvalidArgumentException::class, $e);
		}
	}

	public function testGetLevel() {
		$this->assertEquals(LogLevel::INFO, self::$adapter->getLevel());
	}

	public function testSetAdapterFormat() {
		$format = '[%1$s] - %2$s: %3$s';
		self::$adapter->setFormat($format);
		$this->assertEquals($format, self::$adapter->getFormat());
	}

	public function testSetAdapterTimestamp() {
		$timestamp = 'd-m-Y H:i:s';
		self::$adapter->setTimestamp($timestamp);
		$this->assertEquals($timestamp, self::$adapter->getTimestamp());
	}

	public function testLogWithScalarInterpolation() {
		self::truncateLog();
		# Try to log with the file adapter attached
		$message = 'An unknown error ocurred, code returned: {code}';
		self::$logger->error($message, ['code' => 500]);
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertMatchesRegularExpression('/\[\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\] ERROR: An unknown error ocurred, code returned: 500/', $line);
		} else {
			$this->fail('Failed to open log file');
		}
	}

	public function testLogWithDateTimeInterpolation() {
		self::truncateLog();
		# Try to log with the file adapter attached
		$message = 'An unknown error ocurred, server time: {date}';
		self::$logger->notice($message, ['date' => new DateTime()]);
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertMatchesRegularExpression('/\[\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\] NOTICE: An unknown error ocurred, server time: \d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $line);
		} else {
			$this->fail('Failed to open log file');
		}
	}

	public function testLogWithObjectInterpolation() {
		self::truncateLog();
		# Try to log with the file adapter attached
		$message = 'An unknown error ocurred, object: {obj}';
		self::$logger->warning($message, ['obj' => (object) ['foo' => 'bar']]);
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertMatchesRegularExpression('/\[\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\] WARNING: An unknown error ocurred, object: \[object stdClass\]/', $line);
		} else {
			$this->fail('Failed to open log file');
		}
	}

	public function testLogWithArrayInterpolation() {
		self::truncateLog();
		# Try to log with the file adapter attached
		$message = 'An unknown error ocurred, array: {arr}';
		self::$logger->info($message, ['arr' => ['foo' => 'bar']]);
		# Now check the log file
		$stream = @fopen(self::$path, 'r');
		if ($stream) {
			$line = fgets($stream);
			fclose($stream);
			$this->assertMatchesRegularExpression('/\[\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\] INFO: An unknown error ocurred, array: \[array\]/', $line);
		} else {
			$this->fail('Failed to open log file');
		}
	}
}
