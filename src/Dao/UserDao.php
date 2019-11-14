<?php

namespace App\Dao;

use App\Model\Medecin;
use App\Model\Service;
use App\Model\Specialty;
use \PDO;

class UserDao extends CommonDao
{
    protected $table = "user";
    protected $class = User::class;
}
