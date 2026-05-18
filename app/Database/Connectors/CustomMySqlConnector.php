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
        $sslCa = defined('Pdo\Mysql::ATTR_SSL_CA') ? \Pdo\Mysql::ATTR_SSL_CA : PDO::MYSQL_ATTR_SSL_CA;
        
        $nativePath = '/etc/pki/tls/certs/ca-bundle.crt';
        $fallbackNativePath = '/etc/ssl/certs/ca-certificates.crt';
        $tmpCa = '/tmp/cacert.pem';

        if (!file_exists($tmpCa) || @filesize($tmpCa) < 150000) {
            @copy(base_path('cacert.pem'), $tmpCa);
        }

        $pdo = null;

        // Attempt 1: Native Amazon Linux path (Proven to work in debug-db)
        try {
            if (file_exists($nativePath)) {
                $pdo = new PDO($dsn, $username, $password, [$sslCa => $nativePath]);
            } else {
                throw new \PDOException("Native path $nativePath not found.");
            }
        } catch (\PDOException $e) {
            error_log("Attempt 1 failed: " . $e->getMessage());
            
            // Attempt 2: Fallback Native path
            try {
                if (file_exists($fallbackNativePath)) {
                    $pdo = new PDO($dsn, $username, $password, [$sslCa => $fallbackNativePath]);
                } else {
                    throw new \PDOException("Fallback native path not found.");
                }
            } catch (\PDOException $e2) {
                error_log("Attempt 2 failed: " . $e2->getMessage());
                
                // Attempt 3: Tmp CA
                try {
                    $pdo = new PDO($dsn, $username, $password, [$sslCa => $tmpCa]);
                } catch (\PDOException $e3) {
                    error_log("Attempt 3 failed: " . $e3->getMessage());
                    
                    // Attempt 4: Base CA
                    $pdo = new PDO($dsn, $username, $password, [$sslCa => base_path('cacert.pem')]);
                }
            }
        }

        // Apply remaining options
        if ($pdo) {
            foreach ($options as $key => $value) {
                if ($key !== $sslCa && $key !== 1014) {
                    try {
                        $pdo->setAttribute($key, $value);
                    } catch (\Exception $e) {
                        // ignore setAttribute errors for incompatible options
                    }
                }
            }
        }

        return $pdo;
    }
}
