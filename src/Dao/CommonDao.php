<?php

namespace App\Dao;

use App\Config\DataBase;
use App\Dao\Exception\NotFoundException;

use \PDO;

abstract class CommonDao
{
    protected $pdo;
    protected $table = null;
    protected $class = null;

    function __construct()
    {
        $this->pdo = Database::getPdo();
        if ($this->table === null) {
            throw new \Exception("la class " . get_class($this) . " n'a pas de propriete \$table");
        }
        if ($this->class === null) {
            throw new \Exception("la class " . get_class($this) . " n'a pas de propriete \$class");
        }
    }

    function getMatricule()
    {
        $query = $this->pdo->query("SELECT COUNT(id) FROM user ");
        $nbrDB = $query->fetchColumn();

        $nbr = $nbrDB + 1;
        $nbr = ($nbr >= 1000 ? ("" . $nbr) : ($nbr >= 100 ? ("0" . $nbr) : ($nbr >= 10 ? "00" . $nbr : "000" . $nbr)));

        $d = date("y");

        $val = "" . $d . "GRP" . $nbr;

        return $val;
    }

    public function  findAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE  status=TRUE ORDER BY id DESC";
        $resultats = $this->pdo->query($sql);
        $items = $resultats->fetchAll(PDO::FETCH_CLASS, $this->class);
        //$items = $resultats->fetchAll(PDO::FETCH_ASSOC);
        return $items;
    }

    public  function find(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE status=TRUE AND id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        //$query->setFetchMode(PDO::FETCH_ASSOC);
        $item = $query->fetch();
        if ($item === false) {
            throw new NotFoundException('service', $id);
        }
        return $item;
    }

    public function create(array $data): int
    {
        $sqlFields = [];
        foreach ($data as $key => $value) {
            $sqlFields[] = "$key=:$key";
        }
        $query = $this->pdo->prepare("INSERT INTO {$this->table} SET " . implode(', ', $sqlFields));
        $resultat =  $query->execute($data);
        if (!$resultat) {
            throw new \Exception("impossible de Creer l'enregistrement  dans la table {$this->table}");
        }
        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data, $id): void
    {
        $sqlFields = [];
        foreach ($data as $key => $value) {
            $sqlFields[] = "$key=:$key";
        }
        $query = $this->pdo->prepare("UPDATE  {$this->table} SET " . implode(', ', $sqlFields) . " WHERE id=:id");
        $resultat =  $query->execute(array_merge($data, ['id' => $id]));
        if (!$resultat) {
            throw new \Exception("impossible de Modifier l'enregistrement  dans la table {$this->table}");
        }
    }

    public function delete($id): void
    {
        $query = $this->pdo->prepare("UPDATE {$this->table} SET status=FALSE  WHERE id = :id");
        $resultat =  $query->execute(['id' => $id]);
        if (!$resultat) {
            throw new \Exception("impossible de supprimer l'enregistrement $id dans la table {$this->table}");
        }
    }

    public function exists(string $field, $value, ?int $except = null): bool
    {

        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE status=TRUE AND  $field=?";
        $params = [$value];
        if ($except !== null) {
            $sql .= " AND id !=?";
            $params[] = $except;
        }
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return (int) $query->fetch(PDO::FETCH_NUM)[0] > 0;
    }
}
