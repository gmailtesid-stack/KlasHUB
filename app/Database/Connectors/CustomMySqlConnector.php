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
        if (isset($options[1009]) && $options[1009] === '/tmp/cacert.pem') {
            if (!file_exists('/tmp/cacert.pem') || @filesize('/tmp/cacert.pem') < 150000) {
                @copy(base_path('cacert.pem'), '/tmp/cacert.pem');
            }
        }

        // Isolate SSL options for the constructor to prevent mysqlnd bugs
        $constructorOptions = [];
        if (isset($options[1009])) $constructorOptions[1009] = $options[1009];
        // CRITICAL: Do NOT pass 1014 (MYSQL_ATTR_SSL_VERIFY_SERVER_CERT). 
        // Passing 1014 => false along with 1009 => $caPath causes OpenSSL to conflict and throw 'certificate verify failed'!

        // Return a raw PDO instance to bypass the PHP 8.4/8.5 PDO::connect() bug on Vercel
        $pdo = new PDO($dsn, $username, $password, $constructorOptions);

        // Apply all other options (like ERRMODE, EMULATE_PREPARES) after connection
        foreach ($options as $key => $value) {
            if ($key !== 1009 && $key !== 1014) {
                $pdo->setAttribute($key, $value);
            }
        }

        return $pdo;
    }
}
