<?php

namespace App\Model;

use \DateTime;
use Cocur\Slugify\Slugify;
use JsonSerializable;

class User implements JsonSerializable
{

    protected $id;

    protected $matricule;

    protected $firstName;

    protected $lastName;

    protected $dateNais;

    protected $genre;

    protected $address;

    protected $email;

    protected $login;

    protected $password;

    protected $tel;

    protected $CNI;

    protected $role_id;

    protected $role;

    protected $created_at;

    protected $status = true;


    public function __construct()
    { }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }


    public function getMatricule()
    {
        return $this->matricule;
    }

    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getDateNais(): DateTime
    {
        return new DateTime($this->dateNais);
    }


    public function setDateNais(string $dateNais): self
    {
        $this->dateNais = $dateNais;

        return $this;
    }


    public function getGenre()
    {
        return $this->genre;
    }


    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }


    public function getAddress()
    {
        return $this->address;
    }


    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }


    public function getTel()
    {
        return $this->tel;
    }


    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }


    public function getCNI()
    {
        return $this->CNI;
    }


    public function setCNI($CNI)
    {
        $this->CNI = $CNI;

        return $this;
    }


    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }


    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}
