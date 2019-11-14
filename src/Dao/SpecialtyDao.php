<?php

namespace App\Dao;

use \PDO;
use App\Model\Service;
use App\Model\Specialty;
use App\Dao\PaginatedQuery;

class SpecialtyDao extends CommonDao
{
    protected $table = "specialty";
    protected $class = Specialty::class;

    public function findAllSpecialty()
    {
        $sql = "SELECT * FROM {$this->table} WHERE  status=TRUE ORDER BY id DESC";
        $resultats = $this->pdo->query($sql);
        $specialties = $resultats->fetchAll(PDO::FETCH_CLASS, $this->class);
        foreach ($specialties as  $specialty) {
            $this->hydrateSpecialty($specialty);
        }
        return $specialties;
    }

    public function hydrateSpecialty($specialty): void
    {
        $service = $this->pdo->query(
            "SELECT *
             FROM  service 
             WHERE  status=TRUE 
             AND id={$specialty->getServiceId()}"
        )->fetchAll(PDO::FETCH_CLASS, Service::class)[0];

        $specialty->setService($service);
    }

    public function getServicesIds($services)
    {
        $ids = [];
        foreach ($services as  $service) {
            $ids[] = $service->getId();
        }
        return $ids;
    }

    public function hasChildren($id)
    {
        $query = $this->pdo
            ->prepare(
                'SELECT sp.*,s.id
             FROM specialty sp
             JOIN service s ON sp.service_id=s.id
             WHERE  s.status=TRUE AND sp.status=TRUE
             AND s.id=:id'
            );
        $query->execute(['id' => $id]);
        $result = $query->fetchAll();
        return $result;
    }


    public function list()
    {
        $specialties = $this->findAllSpecialty();
        $specialtiesList = [];
        foreach ($specialties as $specialty) {
            $specialtiesList[$specialty->getId()] = $specialty->getName() . " [ " . 'Service: ' . $specialty->getService()->getName() . " ]";
        }
        return   count($specialtiesList) !== 0 ? $specialtiesList : [];
    }
}
