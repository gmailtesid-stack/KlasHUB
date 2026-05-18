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
        // Ensure the CA file exists on Vercel
        if (isset($options[PDO::MYSQL_ATTR_SSL_CA]) && $options[PDO::MYSQL_ATTR_SSL_CA] === '/tmp/cacert.pem') {
            if (!file_exists('/tmp/cacert.pem') || @filesize('/tmp/cacert.pem') < 150000) {
                @copy(base_path('cacert.pem'), '/tmp/cacert.pem');
            }
        }

        // Return a raw PDO instance to bypass the PHP 8.4/8.5 PDO::connect() bug on Vercel
        return new PDO($dsn, $username, $password, $options);
    }
}
