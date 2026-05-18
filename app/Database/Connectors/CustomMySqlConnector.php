<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\MySqlConnector;
use PDO;

class CustomMySqlConnector extends MySqlConnector
{
    /**
     * Create a new PDO connection instance.
     *
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array  $options
     * @return \PDO
     */
    protected function createPdoConnection($dsn, $username, $password, $options)
    {
        $cleanOptions = [];

        if (isset($options[1009])) {
            $cleanOptions[1009] = $options[1009];
        }

        // Ensure the CA file exists
        if (isset($cleanOptions[1009]) && $cleanOptions[1009] === '/tmp/cacert.pem') {
            if (!file_exists('/tmp/cacert.pem') || @filesize('/tmp/cacert.pem') < 150000) {
                @copy(base_path('cacert.pem'), '/tmp/cacert.pem');
            }
        }

        return new PDO($dsn, $username, $password, $cleanOptions);
    }
}
