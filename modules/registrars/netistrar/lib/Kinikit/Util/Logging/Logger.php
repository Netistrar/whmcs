<?php

namespace Kinikit\Core\Util\Logging;

use Kinikit\Core\Configuration;


/**
 * Simple logger class for writing log entries to the standard configured log file.
 *
 * Class OOALogger
 */
class Logger {

    public static function log($message, $category = "GENERAL") {

        if ($message instanceof \Exception) {
            $category = "ERROR";
            $message = get_class($message) . "\n" . $message->getMessage();
        } else if (is_object($message)) {
            $message = get_class($message) . "\n" . var_export($message, true);
        } else if (is_array($message)) {
            $message = "Array\n" . var_export($message, true);
        }

        $message = "\n" . date("d/m/Y H:i:s") . "\t" . $category . "\t" . $message;

        $fileLocation =
            Configuration::readParameter("log.file") ? Configuration::readParameter("log.file") : "/tmp/application.log";

        // Append a string to the log file.
        file_put_contents($fileLocation, $message, FILE_APPEND);

    }

} 