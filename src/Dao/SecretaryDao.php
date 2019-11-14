<?php

namespace App\Dao;

use App\Dao\Exception\NotFoundException;
use App\Model\Secretary;
use App\Dao\PaginatedQuery;
use App\Model\Service;
use \PDO;

class SecretaryDao extends CommonDao
{
    protected $table = "secretary";
    protected $class = Secretary::class;


    public function findAllSecretary()
    {
        $sql = "SELECT * FROM  secretary s JOIN user u ON s.user_id =u.id WHERE u.status=TRUE ORDER BY u.id DESC";
        $resultats = $this->pdo->query($sql);
        $secretaries = $resultats->fetchAll(PDO::FETCH_CLASS, $this->class);
        foreach ($secretaries as $secretary) {
            $this->hydrateSecretary($secretary);
        }
        return $secretaries;
    }


    public function hydrateSecretary($secretary): void
    {

        $service_id = ($secretary->getServiceId());
        $service = $this->pdo
            ->query("SELECT *FROM  service WHERE  status=TRUE AND id={$service_id}")
            ->fetchAll(PDO::FETCH_CLASS, Service::class)[0];
        $secretary->setService($service);
    }

    public function findbySecretary($id)
    {
        $query = $this->pdo->prepare("SELECT * FROM secretary s JOIN user u ON s.user_id =u.id WHERE u.status=TRUE AND u.id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $item = $query->fetch();
        if ($item === false) {
            throw new NotFoundException('secretary', $id);
        }
        return $item;
    }

    public function createSecretary(array $dataUser, int $idService)
    {
        // try {
        $this->pdo->beginTransaction();
        $sqlFieldsUser = [];
        foreach ($dataUser as $key => $value) {
            $sqlFieldsUser[] = "$key=:$key";
        }
        $query = $this->pdo->prepare("INSERT INTO user SET " . implode(', ', $sqlFieldsUser));
        $query->execute($dataUser);
        $user_id = (int) $this->pdo->lastInsertId();

        $query2 = $this->pdo->prepare("INSERT INTO secretary  SET user_id=:user_id , service_id=:service_id");
        $query2->execute(['user_id' => $user_id, 'service_id' => $idService]);
        $this->pdo->commit();
        // } catch (\Exception $th) {
        //     throw new \Exception("impossible de creer l'enregistrement secretary ");
        // }
        //return (int) $this->pdo->lastInsertId();
    }


    public function updateSecretary(array $dataUser, int $idService, int $idUser, int $idSecretary): void
    {
        //dd($idService,  $idUser,  $idSecretary);
        try {
            $this->pdo->beginTransaction();
            $sqlFieldsUser = [];
            foreach ($dataUser as $key => $value) {
                $sqlFieldsUser[] = "$key=:$key";
            }
            $query = $this->pdo->prepare("UPDATE  user SET " . implode(', ', $sqlFieldsUser) . " WHERE id=:id");
            $query->execute(array_merge($dataUser, ['id' => $idUser]));
            $query2 = $this->pdo->prepare("UPDATE  secretary  SET service_id=:service_id  WHERE secretary_id=:id");
            $query2->execute(['service_id' => $idService, 'id' => $idSecretary]);
            $this->pdo->commit();
        } catch (\Exception $th) {
            throw new \Exception("impossible de Modifier l'enregistrement secretary ");
        }
    }

    public   function deleteSecretary($user_id): void
    {
        $query = $this->pdo->prepare("UPDATE user SET status=FALSE  WHERE id = :id");
        $resultat =  $query->execute(['id' => $user_id]);
        if (!$resultat) {
            throw new \Exception("impossible de supprimer l'enregistrement $user_id dans la table user");
        }
    }

    public function listSecretary()
    {
        $secretaries = $this->findAll("id");
        $secretariesList = [];
        foreach ($secretaries as $secretary) {
            $secretariesList[$secretary->getId()] = $secretary->getName();
        }
        return   count($secretariesList) !== 0 ? $secretariesList : [];
    }
}
