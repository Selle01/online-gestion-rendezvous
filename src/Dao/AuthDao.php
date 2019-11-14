<?php

namespace App\Dao;

use App\Model\Medecin;
use App\Model\Role;
use App\Model\Secretary;
use App\Model\Service;
use App\Model\Specialty;
use App\Model\User;
use App\Session\SessionInterface;
use \PDO;


class AuthDao extends CommonDao
{
    protected $table = "user";
    protected $class = User::class;
    private $session;
    private $user;

    function __construct(SessionInterface $session)
    {
        parent::__construct();
        $this->session = $session;
    }


    public function login($data)
    {
        $query = $this->pdo->prepare("SELECT * FROM user u  WHERE u.login =:login  and u.status=true");
        $query->execute(['login' => $data['login']]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $user = $query->fetch();
        //dd(password_verify($_POST['password'], $user->getPassword()));
        if ($user && password_verify($_POST['password'], $user->getPassword())) {
            //dd($user);
            $this->hydrateUser($user);
            $auth = $this->getAuth($user->getId(), $user->getRole()->getTitle());
            $this->hydrateUser($auth);
            if ($auth->getRole()->getTitle() === 'ROLE_SECRETARY') {
                $auth->setService($this->hydrateService($auth->getServiceId()));
            } else  if ($auth->getRole()->getTitle() === 'ROLE_MEDECIN') {
                $auth->setSpecialty($this->hydrateSpecialty($auth->getSpecialtyId()));
            } else {
                // $auth->setSpecialty($this->hydrateSpecialty($auth->getSpecialtyId()));
                // $auth->setService($this->hydrateService($auth->getServiceId()));
            }
            $this->session->set('auth.user', ($auth));

            return $auth;
        }
        return null;
    }


    public function getAuth($userId, $role)
    {
        $sql = "";
        $className = "";
        switch ($role) {
            case 'ROLE_ADMIN':
                $sql = "SELECT * FROM user  WHERE status=TRUE AND id = :id";
                $className = User::class;
                break;
            case 'ROLE_MEDECIN':
                $sql = "SELECT * FROM medecin m JOIN user u ON m.user_id =u.id WHERE status=TRUE AND u.id = :id";
                $className = Medecin::class;
                break;
            case 'ROLE_SECRETARY':
                $sql = "SELECT * FROM secretary s JOIN user u ON s.user_id =u.id WHERE u.status=TRUE AND u.id = :id";
                $className = Secretary::class;
                break;
            default:
                # code...
                break;
        }
        $query = $this->pdo->prepare($sql);
        $query->execute(['id' =>  $userId]);
        $query->setFetchMode(PDO::FETCH_CLASS,  $className);
        return $query->fetch();
    }


    public  function hydrateUser($user)
    {
        $query = $this->pdo->prepare("SELECT * FROM  role WHERE status=true  AND id=:id");
        $query->execute(['id' =>  $user->getRoleId()]);
        $query->setFetchMode(PDO::FETCH_CLASS, Role::class);
        $role = $query->fetch();
        $user->setRole($role);
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
        $query->setFetchMode(PDO::FETCH_CLASS, Service::class);
        $service = $query->fetch();
        return $service;
    }
}
