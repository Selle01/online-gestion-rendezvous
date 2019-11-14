<?php

namespace App\Dao;

use App\Model\Role;
use PDO;

class RoleDao extends CommonDao
{
    protected $table = "role";
    protected $class = Role::class;


    public function listFind($title)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE status=TRUE AND title = :title");
        $query->execute(['title' => $title]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        //$query->setFetchMode(PDO::FETCH_ASSOC);
        $role = $query->fetch();
        $rolesList = [];
        $rolesList[$role->getId()] = $role->getTitle();
        // dd($rolesList);
        return   count($rolesList) !== 0 ? $rolesList : [];
    }

    public function list()
    {
        $roles = $this->findAll();
        $rolesList = [];
        foreach ($roles as $role) {
            $rolesList[$role->getId()] = $role->getTitle();
        }
        return   count($rolesList) !== 0 ? $rolesList : [];
    }
}
