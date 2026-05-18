<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\MySqlConnector;
use PDO;

class CustomMySqlConnector extends MySqlConnector
{
    /**
     * Create a new PDO connection.
     *
     * @param  string  $dsn
     * @param  array  $config
     * @param  array  $options
     * @return \PDO
     */
    public function createConnection($dsn, array $config, array $options)
    {
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;
        
        $minimalOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            1009 => '/tmp/cacert.pem', // MYSQL_ATTR_SSL_CA
        ];

        return new PDO($dsn, $username, $password, $minimalOptions);
    }
}
