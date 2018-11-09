<?php namespace Awebsome\Serverpilot\Classes;

use Exception;

/**
 * ServerPilot Exceptions
 */
class ServerPilotException extends Exception {
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        $message = '[ServerPilot]: ' . $message;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}