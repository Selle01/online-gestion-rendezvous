<?php

namespace App\Model;

use \DateTime;
use App\Model\Specialty;
use Cocur\Slugify\Slugify;
use JsonSerializable;

class Service implements JsonSerializable
{

    private $id;
    private $name;
    private $created_at;
    private $specialties = [];
    private $status;

    public function __construct()
    {
        //  $this->created_at =   new DateTime();
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }


    public function getName()
    {
        return $this->name;
    }


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }


    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = new DateTime($created_at);
        return $this;
    }


    public function getSlug(): ?string
    {
        $slugify = new Slugify();
        return  $slugify->slugify($this->name);
    }


    public function getSpecialties(): array
    {
        return $this->specialties;
    }

    public function setSpecialties(array $specialties): self
    {
        $this->specialties = $specialties;
        return $this;
    }


    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function addSpecialty(Specialty $specialty)
    {
        if (!in_array($specialty, $this->specialties)) {
            $this->specialties[] = $specialty;
            $specialty->setService($this);
        }
    }
}
