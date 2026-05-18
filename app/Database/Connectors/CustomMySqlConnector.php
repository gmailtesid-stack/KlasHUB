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

        // Force raw 'new PDO' connection instead of PHP 8.4+ 'PDO::connect()' 
        // to bypass the SSL verification bug in the PHP 8.5/8.4 connect function on Vercel.
        return new PDO($dsn, $username, $password, $options);
    }
}
