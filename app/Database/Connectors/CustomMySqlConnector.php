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
        
        throw new \Exception("DIAGNOSTIC - DSN: {$dsn} | Username: {$username} | Options: " . json_encode($options));
    }
}
