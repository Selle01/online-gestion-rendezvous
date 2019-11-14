<?php

namespace App\Dao;

use App\Model\Medecin;
use App\Model\Patient;
use App\Model\Rendezvous;
use App\Model\Secretary;
use App\Model\Service;
use App\Model\Specialty;
use \PDO;

class RendezVousDao extends CommonDao
{
    protected $table = "rendezvous";
    protected $class = Rendezvous::class;

    public function findAllRendezVous()
    {
        $sql = "SELECT * FROM  rendezvous rv 
                    JOIN secretary s ON rv.secretary_id =s.secretary_id
                    JOIN medecin m ON rv.medecin_id =m.id
                    JOIN patient p ON rv.patient_id =p.patient_id
                 ORDER BY rv.rv_id DESC";
        $resultats = $this->pdo->query($sql);
        $rendezVous = $resultats->fetchAll(PDO::FETCH_CLASS, $this->class);
        foreach ($rendezVous as $rv) {
            $this->hydrateRendezVous($rv);
        }
        return $rendezVous;
    }


    public function hydrateRendezVous($rendezVous): void
    {
        $secretary_id = $rendezVous->getSecretaryId();
        $medecin_id = $rendezVous->getMedecinId();
        $patient_id = $rendezVous->getPatientId();
        $secretary = $this->pdo
            ->query("SELECT * FROM  secretary s
             JOIN rendezvous rv ON s.secretary_id =rv.secretary_id
             JOIN user u ON s.user_id =u.id WHERE u.status=TRUE
             AND rv.status=TRUE   AND s.secretary_id={$secretary_id}")
            ->fetchAll(PDO::FETCH_CLASS, Secretary::class)[0];
        $rendezVous->setSecretary($secretary);
        $medecin = $this->pdo
            ->query("SELECT * FROM  medecin m 
            JOIN rendezvous rv ON m.id =rv.medecin_id
            JOIN user u ON m.user_id =u.id WHERE u.status=TRUE
             AND rv.status=TRUE AND m.id={$medecin_id}")
            ->fetchAll(PDO::FETCH_CLASS, Medecin::class)[0];
        $rendezVous->setMedecin($medecin);
        $patient = $this->pdo
            ->query("SELECT * FROM  patient p 
            JOIN rendezvous rv ON p.patient_id =rv.medecin_id
            JOIN user u ON p.user_id =u.id WHERE u.status=TRUE
             AND rv.status=TRUE AND p.patient_id={$patient_id}")
            ->fetchAll(PDO::FETCH_CLASS, Patient::class)[0];
        $rendezVous->setPatient($patient);
    }

    public function findbyRendezVous($id)
    {
        $query = $this->pdo->prepare("SELECT * FROM rendezvous  WHERE status=TRUE AND rv_id= :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $item = $query->fetch();
        return $item;
    }

    public function createRendezVous(array $dataRendezVous)
    {
        $sqlFieldsRendezVous = [];
        foreach ($dataRendezVous as $key => $value) {
            $sqlFieldsRendezVous[] = "$key=:$key";
        }
        // dd($sqlFieldsRendezVous, $dataRendezVous);
        $query = $this->pdo->prepare("INSERT INTO rendezvous SET " . implode(', ', $sqlFieldsRendezVous));
        $query->execute($dataRendezVous);
    }


    public function updateRendezVous(array $dataRendezVous): void
    {
        $sqlFieldsRendezVous = [];
        foreach ($dataRendezVous as $key => $value) {
            if ($key !== 'rv_id') {
                $sqlFieldsRendezVous[] = "$key=:$key";
            }
        }
        //dd($sqlFieldsRendezVous, $dataRendezVous);
        $query = $this->pdo->prepare("UPDATE rendezvous SET " . implode(', ', $sqlFieldsRendezVous) . " WHERE rv_id=:rv_id");
        $query->execute($dataRendezVous);
    }



    public function listofMedecinAll()
    {
        $query = $this->pdo->query("SELECT * FROM medecin m JOIN user u ON m.user_id =u.id WHERE status=TRUE ");
        $medecins = $query->fetchAll(PDO::FETCH_CLASS, Medecin::class);
        $medecinsList = [];
        foreach ($medecins  as $medecin) {
            $medecinsList[$medecin->getId()] =  $medecin->getFirstName() . ' ' . $medecin->getLastName() . '=> Matricule: ' . $medecin->getMatricule();
        }
        return   count($medecinsList) !== 0 ? $medecinsList : [];
    }

    public function listofMedecinByService($service_id)
    {
        $sql = ("SELECT * FROM specialty   WHERE service_id=:id");
        $query = $this->pdo->prepare($sql);
        $query->execute(['id' => $service_id]);
        $specialties = $query->fetchAll(PDO::FETCH_CLASS, Specialty::class);
        $medecinsList = [];
        foreach ($specialties as  $specialty) {
            $specialty->setService($this->hydrateService($service_id));
            $specialty->setMedecins($this->hydrateMedecin($specialty->getId()));
        }

        foreach ($specialty->getMedecins() as $medecin) {
            $medecinsList[$medecin->getId()] =  $medecin->getFirstName() . ' ' . $medecin->getLastName() . '=> Matricule: ' . $medecin->getMatricule();
        }
        return   count($medecinsList) !== 0 ? $medecinsList : [];
    }



    public function listofPatient()
    {
        $sql = "SELECT * FROM  patient p JOIN user u ON p.user_id =u.id WHERE u.status=TRUE ";
        $resultats = $this->pdo->query($sql);
        $patients = $resultats->fetchAll(PDO::FETCH_CLASS, Patient::class);
        $patientsList = [];
        foreach ($patients as $patient) {
            $patientsList[$patient->getPatientId()] =   $patient->getFirstName() . ' ' . $patient->getLastName() . '=> Matricule: ' . $patient->getMatricule();
        }
        return   count($patientsList) !== 0 ? $patientsList : [];
    }

    public function listofSecretaryByService($service_id, $secretary_id)
    {
        $sql = "SELECT * FROM  secretary s JOIN user u ON s.user_id =u.id WHERE u.status=TRUE";
        if ($service_id) {
            $sql .= " AND  s.service_id=" . $service_id;
            $sql .= " AND  s.secretary_id=" . $secretary_id;
        }
        $resultats = $this->pdo->query($sql);
        $secretaries = $resultats->fetchAll(PDO::FETCH_CLASS, Secretary::class);
        $secretariesList = [];
        foreach ($secretaries as $secretary) {
            $secretariesList[(int) $secretary->getSecretaryId()] =   $secretary->getFirstName() . ' ' . $secretary->getLastName() . '=> Matricule: ' . $secretary->getMatricule();
        }
        return   count($secretariesList) !== 0 ? $secretariesList : [];
    }

    public function listofSecretaryAll()
    {
        $query = $this->pdo->query("SELECT * FROM secretary s JOIN user u ON s.user_id =u.id WHERE u.status=TRUE");
        $secretaries = $query->fetchAll(PDO::FETCH_CLASS, Secretary::class);
        $secretariesList = [];
        foreach ($secretaries as $secretary) {
            $secretariesList[(int) $secretary->getSecretaryId()] =   $secretary->getFirstName() . ' ' . $secretary->getLastName() . '=> Matricule: ' . $secretary->getMatricule();
        }
        return   count($secretariesList) !== 0 ? $secretariesList : [];
    }


    public function hydrateSpecialty($specialty_id)
    {
        $sql = ("SELECT sp.*,s.id
             FROM specialty sp
             JOIN service s ON sp.service_id=s.id
             WHERE  s.status=TRUE AND sp.status=TRUE
             AND sp.id=:id");
        $query = $this->pdo->prepare($sql);
        $query->execute(['id' => $specialty_id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Specialty::class);
        $specialty = $query->fetch();
        return $specialty;
    }

    public function hydrateService($service_id)
    {
        $sql = ("SELECT * FROM service   WHERE id=:id");
        $query = $this->pdo->prepare($sql);
        $query->execute(['id' => $service_id]);
        $service = $query->fetchAll(PDO::FETCH_CLASS, Service::class);
        //dd($service);

        return $service;
    }

    public function hydrateMedecin($specialty_id)
    {
        $sql = "SELECT * FROM  medecin m   
        JOIN specialty s ON m.specialty_id =s.id 
         JOIN user u ON m.user_id =u.id 
         WHERE s.id=:id
         AND  u.status=TRUE
         AND  u.status=TRUE";
        $query = $this->pdo->prepare($sql);
        $query->execute(['id' => $specialty_id]);
        return   $query->fetchAll(PDO::FETCH_CLASS, Medecin::class);
    }


    public   function deleteRendezVous($rv_id): void
    {
        $query = $this->pdo->prepare("UPDATE rendezvous SET status=FALSE  WHERE rv_id=:id");
        $resultat =  $query->execute(['id' => $rv_id]);
        //dd($resultat);
        if (!$resultat) {
            throw new \Exception("impossible de supprimer l'enregistrement $rv_id dans la table user");
        }
    }
}
