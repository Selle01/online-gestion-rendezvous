<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\SecretaryValidator;
use App\Components\Validators\UserValidator;
use App\Dao\SecretaryDao;
use App\Dao\RoleDao;
use App\Dao\ServiceDao;
use App\Dao\SpecialtyDao;
use App\Dao\UserDao;
use App\Model\CommonModel;
use App\Model\Secretary;
use App\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Router;
use App\Router\RouterAction;

class SecretaryController
{

    private $renderer;
    private $router;
    use RouterAction;
    private $secretaryDao;
    private $role;
    private  $services;
    private $genres = [
        'FEMME' => 'FEMME',
        'HOMME' => 'HOMME'
    ];

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->secretaryDao = new SecretaryDao();
        $this->role = (new RoleDao())->listFind('ROLE_SECRETARY');
        $this->services = (new SpecialtyDao())->list();
        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'secretary');
        $this->router->crud('secretary', $this, 'secretary');
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $slug = $request->getAttribute('id');
        if ($slug) {
            return $this->show($request);
        }
        return $this->index($request);
    }

    public function index(): string
    {
        $secretary = new Secretary();
        $matricule = $this->secretaryDao->getMatricule();
        $form = new Form($secretary);
        $services = (new ServiceDao())->list();
        $this->renderer->currentMenu('secretary');
        $role = $this->role;
        return $this->renderer->render('@secretary/index', compact('role', 'services', 'secretary', 'form', 'matricule'));
    }

    public function getMatricule()
    {
        $matricule = $this->secretaryDao->getMatricule();
        return json_encode($matricule, true);
    }

    public function secretaryFindAll()
    {
        $secretaries = $this->secretaryDao->findAllSecretary();
        return json_encode(['data' => $secretaries], true);
    }


    public function new(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $secretary = new Secretary();
            $secretary->setMatricule($this->secretaryDao->getMatricule());
            $services = (new ServiceDao())->list();
            $v = new SecretaryValidator($_POST, (new UserDao()), $secretary->getId(), $this->genres, $services);
            $fields = $this->getFields();
            CommonModel::hydrate($secretary, $_POST, $fields);
            if ($v->validate()) {
                $idInsert = $this->secretaryDao->createSecretary(
                    $this->getData($secretary),
                    $_POST['service_id']
                );
                $secretary->getUserId($idInsert);
                return json_encode(['action' => 'success'], true);
            } else {
                $errors[] = $v->errors();
                return json_encode(['action' => 'error', 'errors' => $errors], true);
            }
        }
    }

    public function edit(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            $id = (int) $request->getAttribute('id');
            $secretary = $this->secretaryDao->findbySecretary($id);
            $this->secretaryDao->hydrateSecretary($secretary);
            return json_encode($secretary, true);
        } else if ($request->getMethod() === 'POST') {
            $errors = [];
            $id = $request->getAttribute('id');
            $secretary = $this->secretaryDao->findbySecretary($id);
            //dd($secretary);
            $services = (new ServiceDao())->list();
            $v = new SecretaryValidator($_POST, (new UserDao()), $secretary->getId(), $this->genres, $services);
            $fields = $this->getFields();
            CommonModel::hydrate($secretary, $_POST, $fields);
            if ($v->validate()) {
                $this->secretaryDao->updateSecretary(
                    $this->getData($secretary),
                    $_POST['service_id'],
                    $secretary->getUserId(),
                    $secretary->getSecretaryId()
                );
                return json_encode(['action' => 'success'], true);
            } else {
                $errors[] = $v->errors();
                return json_encode(['action' => 'error', 'errors' => $errors], true);
            }
        }
    }

    public function delete(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'DELETE') {
            try {
                $id = $request->getAttribute('id');
                //if (count($this->secretaryDao->hasChildren($id)) === 0) {
                $this->secretaryDao->deleteSecretary($id);
                return json_encode(['action' => 'success'], true);
                exit();
                // }
            } catch (\Throwable $th) {
                return json_encode(['action' => 'eroors'], true);
            }
        }
    }

    public function show(ServerRequestInterface $request)
    { }

    private function getFields()
    {
        return [
            'matricule',
            'firstName',
            'lastName',
            'dateNais',
            'genre',
            'address',
            'email',
            'login',
            'password',
            'tel',
            'CNI',
            'created_at',
            'role_id',
        ];
    }

    private function getData($item)
    {
        return   [
            'matricule' => $item->getMatricule(),
            'firstName' => $item->getFirstName(),
            'lastName' => $item->getLastName(),
            'dateNais' => $item->getDateNais()->format('Y-m-d H:i:s'),
            'genre' => $item->getGenre(),
            'address' => $item->getAddress(),
            'email' =>  $item->getEmail(),
            'login' => $item->getLogin(),
            'password' => password_hash($item->getPassword(), PASSWORD_DEFAULT),
            'tel' => $item->getTel(),
            'CNI' => $item->getCNI(),
            'status' => $item->getStatus(),
            'created_at' => $item->getCreatedAt()->format('Y-m-d H:i:s'),
            'role_id' => $item->getRoleId(),
        ];
    }
}
