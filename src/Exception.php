<?php
namespace Chadicus\Util;

/**
 * Static utility class for exceptions.
 */
class Exception
{
    /**
     * Returns the Exception that is the root cause of one or more subsequent exceptions.
     *
     * @param \Exception $exception The exception of which to find a base exception.
     *
     * @return \Exception
     */
    final public static function getBaseException(\Exception $exception)
    {
        while ($exception->getPrevious() !== null) {
            $exception = $exception->getPrevious();
        }

        return $exception;
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
     * @throws \ErrorException Thrown based on information given in parameters.
     */
    final public static function raise($level, $message, $file = null, $line = null)
    {
        if (error_reporting() === 0) {
            return false;
        }

        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Converts the given Exception to an array.
     *
     * @param \Exception $exception     The exception to convert.
     * @param boolean    $traceAsString Flag to return the exception trace as a string or array.
     * @param integer    $depth         User specified recursion depth.
     *
     * @return array
     */
    final public static function toArray(\Exception $exception, $traceAsString = false, $depth = 512)
    {
        $result = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $traceAsString ? $exception->getTraceAsString() : $exception->getTrace(),
            'previous' => null,
        ];

        if ($exception->getPrevious() !== null && --$depth) {
            $result['previous'] = self::toArray($exception->getPrevious(), $traceAsString, $depth);
        }

        return $result;
    }

    /**
     * Creates an ErrorException based on the error from error_get_last().
     *
     * @return \ErrorException
     */
    final public static function fromLastError()
    {
        $error = error_get_last();
        return new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
    }
}
