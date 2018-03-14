<?php
namespace Chadicus\Util;

use ErrorException;
use Throwable;

/**
 * Static utility class for exceptions.
 */
abstract class Exception
{
    /**
     * Returns the Exception that is the root cause of one or more subsequent exceptions.
     *
     * @param Throwable $throwable The exception/error of which to find a base exception.
     *
     * @return Throwable
     */
    final public static function getBaseException(Throwable $throwable) : Throwable
    {
        while ($throwable->getPrevious() !== null) {
            $throwable = $throwable->getPrevious();
        }

        return $throwable;
    }

    /**
     * Throws a new \ErrorException based on the error information provided.
     *
     * @param integer $level   The level of the error raised.
     * @param string  $message The error message.
     * @param string  $file    The filename from which the error was raised.
     * @param integer $line    The line number at which the error was raised.
     *
     * @return bool false
     *
     * @throws ErrorException Thrown based on information given in parameters.
     */
    final public static function raise(int $level, string $message, string $file = null, int $line = null)
    {
        if (error_reporting() === 0) {
            return false;
        }

        throw new ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Converts the given Exception to an array.
     *
     * @param Throwable $throwable     The exception to convert.
     * @param boolean   $traceAsString Flag to return the exception trace as a string or array.
     * @param integer   $depth         User specified recursion depth.
     *
     * @return array
     */
    final public static function toArray(Throwable $throwable, bool $traceAsString = false, int $depth = 512) : array
    {
        $result = [
            'type' => get_class($throwable),
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $traceAsString ? $throwable->getTraceAsString() : $throwable->getTrace(),
            'previous' => null,
        ];

        if ($throwable->getPrevious() !== null && --$depth) {
            $result['previous'] = self::toArray($throwable->getPrevious(), $traceAsString, $depth);
        }

        return $result;
    }

    /**
     * Creates an ErrorException based on the error from error_get_last().
     *
     * @return \ErrorException|null
     */
    final public static function fromLastError()
    {
        $error = error_get_last();
        if ($error !== null) {
            return new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        }
    }
}
