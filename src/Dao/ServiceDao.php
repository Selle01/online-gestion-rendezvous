<?php

namespace App\Dao;

use \PDO;
use App\Model\Service;
use App\Model\Specialty;

class ServiceDao extends CommonDao
{
    protected $table = "service";
    protected $class = Service::class;

    public function hydrateServices(array $services): void
    {
        $servicesByID = [];
        foreach ($services as  $service) {
            $service->setSpecialties([]);
            $servicesByID[$service->getId()] = $service;
        }

        $specialties = $this->pdo->query(
            'SELECT sp.*,s.id
             FROM specialty sp
             JOIN service s ON sp.service_id=s.id
             WHERE  s.status=TRUE AND sp.status=TRUE
             AND s.id IN(' . implode(',', array_keys($servicesByID)) . ')'
        )->fetchAll(PDO::FETCH_CLASS, Specialty::class);

        foreach ($specialties as $specialty) {
            $servicesByID[$specialty->getServiceId()]->addSpecialty($specialty);
        }
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
        $services = $this->findAll("id");
        $servicesList = [];
        foreach ($services as $service) {
            $servicesList[$service->getId()] = $service->getName();
        }
        return   count($servicesList) !== 0 ? $servicesList : [];
    }
}
