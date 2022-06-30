<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;


class DebugHelper {

	/**
	 * returns the string data of an exception
	 * @param Exception $exception
	 * @param $customData
	 *
	 * @return string
	 */
	public static function getExceptionData( Exception $exception, $customData ) {
		return '  ## MESSAGE: ' . $exception->getMessage() .
		       '  ## CODE: ' . $exception->getCode() .
		       '  ## FILE: ' . $exception->getFile() .
		       '  ## LINE: ' . $exception->getLine() .
		       '  ## CUSTOM DATA: ' . $customData;
	}

	/**
	 * Logs the data of an exception without Trace
	 * @param Exception $exception
	 * @param string $customData
	 */
	public static function logException( Exception $exception, $customData = '' ) {
		Log::info( ' ## LOGGER: ' . self::getExceptionData($exception, $customData) );
	}

	/**
	 * Logs the data of an exception with trace
	 * @param Exception $exception
	 * @param string $customData
	 */
	public static function logExceptionWithTrace( Exception $exception, $customData = '' ) {
		Log::info( ' ## LOGGER: ' . self::getExceptionData($exception, $customData) .
		           '  ## TRACE: ' . $exception->getTraceAsString()
		);
	}
}
