<?php

namespace App\Dao;

use App\Model\Patient;
use App\Dao\PaginatedQuery;
use \PDO;

class PatientDao extends CommonDao
{
    protected $table = "patient";
    protected $class = Patient::class;

    public function findAllPatient()
    {
        $sql = "SELECT * FROM  patient p JOIN user u ON p.user_id =u.id WHERE u.status=TRUE ORDER BY u.id DESC";
        $resultats = $this->pdo->query($sql);
        $patients = $resultats->fetchAll(PDO::FETCH_CLASS, $this->class);
        return $patients;
    }


    public function findbyPatient($id)
    {
        $query = $this->pdo->prepare("SELECT * FROM patient p JOIN user u ON p.user_id =u.id WHERE status=TRUE AND u.id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $item = $query->fetch();
        if ($item === false) {
            throw new NotFoundException('patient', $id);
        }
        return $item;
    }

    public function createPatient(array $dataUser)
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

        $query2 = $this->pdo->prepare("INSERT INTO patient  SET user_id=:user_id ");
        $query2->execute(['user_id' => $user_id, 'user_id' => $user_id]);
        $this->pdo->commit();
        // } catch (\Exception $th) {
        //     throw new \Exception("impossible de creer l'enregistrement medecin ");
        // }
        //return (int) $this->pdo->lastInsertId();
    }


    public function updatePatient(array $dataUser, int $idUser): void
    {
        try {
            $this->pdo->beginTransaction();
            $sqlFieldsUser = [];
            foreach ($dataUser as $key => $value) {
                $sqlFieldsUser[] = "$key=:$key";
            }
            $query = $this->pdo->prepare("UPDATE  user SET " . implode(', ', $sqlFieldsUser) . " WHERE id=:id");
            $query->execute(array_merge($dataUser, ['id' => $idUser]));
            $this->pdo->commit();
        } catch (\Exception $th) {
            throw new \Exception("impossible de Modifier l'enregistrement medecin ");
        }
    }

    public   function deletePatient($user_id): void
    {
        $query = $this->pdo->prepare("UPDATE user SET status=FALSE  WHERE id = :id");
        $resultat =  $query->execute(['id' => $user_id]);
        if (!$resultat) {
            throw new \Exception("impossible de supprimer l'enregistrement $user_id dans la table user");
        }
    }

    public function listPatient()
    {
        $patients = $this->findAll("id");
        $patientsList = [];
        foreach ($patients as $patient) {
            $patientsList[$patient->getId()] = $patient->getName();
        }
        return   count($patientsList) !== 0 ? $patientsList : [];
    }
}
