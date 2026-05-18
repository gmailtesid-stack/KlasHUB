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
        
        $caPath = '/tmp/cacert.pem';
        $sourcePath = base_path('cacert.pem');
        
        // Ensure /tmp/cacert.pem is fully written and not corrupted (size should be ~189KB)
        if (!file_exists($caPath) || @filesize($caPath) < 150000) {
            @copy($sourcePath, $caPath);
        }

        $minimalOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            1009 => $caPath, // PDO::MYSQL_ATTR_SSL_CA
        ];

        return new PDO($dsn, $username, $password, $minimalOptions);
    }
}
