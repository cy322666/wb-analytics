<?php

namespace App\Services\DB;

use App\Models\Account;
use Illuminate\Support\Facades\Config;

class Manager
{
    public function init($database, string $connectionName = 'second')
    {
        $connectionFullName = 'database.connections.'.$connectionName;

        Config::set($connectionFullName.'.port',     $database->db_port);
        Config::set($connectionFullName.'.username', $database->db_username);
        Config::set($connectionFullName.'.database', $database->db_name);
        Config::set($connectionFullName.'.password', $database->db_password);
        Config::set($connectionFullName.'.driver',   $database->db_type);
        Config::set($connectionFullName.'.host',     $database->db_host);

        Config::set('database.default', $connectionName);
    }
}
