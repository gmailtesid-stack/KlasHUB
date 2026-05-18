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
        $tmpCa = '/tmp/cacert.pem';
        $baseCa = base_path('cacert.pem');

        if (!file_exists($tmpCa) || @filesize($tmpCa) < 150000) {
            @copy($baseCa, $tmpCa);
        }

        try {
            // Attempt 1: Exactly like pdo_tmp_success
            $pdo = new PDO($dsn, $username, $password, [
                1009 => $tmpCa
            ]);
        } catch (\PDOException $e) {
            try {
                // Attempt 2: Exactly like pdo_base_success
                $pdo = new PDO($dsn, $username, $password, [
                    1009 => $baseCa
                ]);
            } catch (\PDOException $e2) {
                // Attempt 3: Try with both
                $pdo = new PDO($dsn, $username, $password, [
                    1009 => $tmpCa,
                    1014 => false
                ]);
            }
        }

        // Apply remaining options
        foreach ($options as $key => $value) {
            if ($key !== 1009 && $key !== 1014) {
                $pdo->setAttribute($key, $value);
            }
        }

        return $pdo;
    }
}
