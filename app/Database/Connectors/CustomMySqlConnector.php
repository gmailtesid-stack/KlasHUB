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
        
        $caPath = file_exists('/etc/pki/tls/certs/ca-bundle.crt') 
            ? '/etc/pki/tls/certs/ca-bundle.crt' 
            : (file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : base_path('cacert.pem'));

        $minimalOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            1014 => false,   // PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT (Disables server certificate verification)
        ];

        return new PDO($dsn, $username, $password, $minimalOptions);
    }
}
