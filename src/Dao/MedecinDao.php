<?php

namespace App\Dao;

use App\Model\Medecin;
use App\Model\Service;
use App\Model\Specialty;
use \PDO;

class MedecinDao extends CommonDao
{
    protected $table = "medecin";
    protected $class = Medecin::class;

    public function findAllMedecin()
    {
        $sql = "SELECT  m.*,u.*, m.id as medecin_id FROM  medecin m  JOIN user u ON m.user_id =u.id WHERE u.status=TRUE ORDER BY m.id DESC";
        $resultats = $this->pdo->query($sql);
        $medecins = $resultats->fetchAll(PDO::FETCH_CLASS, $this->class);
        foreach ($medecins as $medecin) {
            $this->hydrateMedecin($medecin);
        }
        //dd($medecins);
        return $medecins;
    }


    public function hydrateMedecin($medecin): void
    {
        $specialty_id = ($medecin->getSpecialtyId());
        $specialty = $this->pdo
            ->query("SELECT * FROM  specialty WHERE  status=TRUE  AND id={$specialty_id}")
            ->fetchAll(PDO::FETCH_CLASS, Specialty::class)[0];
        $service_id = ($specialty->getServiceId());
        $service = $this->pdo
            ->query("SELECT *FROM  service WHERE  status=TRUE AND id={$service_id}")
            ->fetchAll(PDO::FETCH_CLASS, Service::class)[0];
        $specialty->setService($service);
        $medecin->setSpecialty($specialty);
    }

    public function findbyMedecin($id)
    {
        $query = $this->pdo->prepare("SELECT m.*,u.*, m.id as medecin_id FROM medecin m JOIN user u ON m.user_id =u.id WHERE status=TRUE AND m.id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $item = $query->fetch();
        if ($item === false) {
            throw new NotFoundException('medecin', $id);
        }

        return $item;
    }

    public function createMedecin(array $dataUser, int $idspecialty)
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

        $query2 = $this->pdo->prepare("INSERT INTO medecin  SET user_id=:user_id ,specialty_id=:idspecialty");
        $query2->execute(['user_id' => $user_id, 'idspecialty' => $idspecialty]);
        $this->pdo->commit();
        // } catch (\Exception $th) {
        //     throw new \Exception("impossible de creer l'enregistrement medecin ");
        // }
        //return (int) $this->pdo->lastInsertId();
    }


    public function updateMedecin(array $dataUser, int $idSpecialty, int $idUser, int $idMedecin): void
    {
        try {
            $this->pdo->beginTransaction();
            $sqlFieldsUser = [];
            foreach ($dataUser as $key => $value) {
                $sqlFieldsUser[] = "$key=:$key";
            }
            $query = $this->pdo->prepare("UPDATE  user SET " . implode(', ', $sqlFieldsUser) . " WHERE id=:id");
            $query->execute(array_merge($dataUser, ['id' => $idUser]));
            $query2 = $this->pdo->prepare("UPDATE  medecin  SET specialty_id=:specialty_id  WHERE id=:id");
            $query2->execute(['specialty_id' => $idSpecialty, 'id' => $idMedecin]);
            $this->pdo->commit();
        } catch (\Exception $th) {
            throw new \Exception("impossible de Modifier l'enregistrement medecin ");
        }
    }

    public   function deleteMedecin($user_id): void
    {
        $query = $this->pdo->prepare("UPDATE user SET status=FALSE  WHERE id = :id");
        $resultat =  $query->execute(['id' => $user_id]);
        if (!$resultat) {
            throw new \Exception("impossible de supprimer l'enregistrement $user_id dans la table user");
        }
    }

    public function listMedecin()
    {
        $medecins = $this->findAll("id");
        $medecinsList = [];
        foreach ($medecins as $medecin) {
            $medecinsList[$medecin->getId()] = $medecin->getName();
        }
        return   count($medecinsList) !== 0 ? $medecinsList : [];
    }
}
