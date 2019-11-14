<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\MedecinValidator;
use App\Dao\MedecinDao;
use App\Dao\RoleDao;
use App\Dao\SpecialtyDao;
use App\Dao\UserDao;
use App\Model\CommonModel;
use App\Model\Medecin;
use App\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Router;
use App\Router\RouterAction;

class MedecinController
{

    private $renderer;
    private $router;
    use RouterAction;
    private $medecinDao;
    private $role;
    private  $specialties;
    private $genres = [
        'FEMME' => 'FEMME',
        'HOMME' => 'HOMME'
    ];

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->medecinDao = new MedecinDao();
        $this->role = (new RoleDao())->listFind('ROLE_MEDECIN');
        $this->specialties = (new SpecialtyDao())->list();
        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'medecin');
        $this->router->crud('medecin', $this, 'medecin');
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
        $medecin = new Medecin();
        $matricule = $this->medecinDao->getMatricule();
        $form = new Form($medecin);
        $specialties = (new SpecialtyDao())->list();
        $this->renderer->currentMenu('medecin');
        $role = $this->role;
        return $this->renderer->render('@medecin/index', compact('role', 'specialties', 'medecin', 'form', 'matricule'));
    }

    public function getMatricule()
    {
        $matricule = $this->medecinDao->getMatricule();
        return json_encode($matricule, true);
    }

    public function medecinFindAll()
    {
        $medecins = $this->medecinDao->findAllMedecin();
        return json_encode(['data' => $medecins], true);
    }


    public function new(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $medecin = new Medecin();
            $medecin->setMatricule($this->medecinDao->getMatricule());
            $medecin->setRoleId(array_keys($this->role)[0]);
            $specialties = (new SpecialtyDao())->list();
            $v = new MedecinValidator($_POST, (new UserDao()),  $medecin->getId(), $this->genres, $specialties);
            $fields = $this->getFields();
            CommonModel::hydrate($medecin, $_POST, $fields);
            if ($v->validate()) {
                $idInsert = $this->medecinDao->createMedecin(
                    $this->getData($medecin),
                    $_POST['specialty_id']
                );
                $medecin->getUserId($idInsert);
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
            $id = $request->getAttribute('id');
            $medecin = $this->medecinDao->findbyMedecin($id);
            $this->medecinDao->hydrateMedecin($medecin);
            return json_encode($medecin, true);
        } else if ($request->getMethod() === 'POST') {
            $errors = [];
            $id = $request->getAttribute('id');
            $medecin = $this->medecinDao->findbyMedecin($id);
            //dd($medecin);
            $specialties = (new SpecialtyDao())->list();
            $v = new MedecinValidator($_POST, (new UserDao()), $medecin->getId(), $this->genres, $specialties);
            $fields = $this->getFields();
            CommonModel::hydrate($medecin, $_POST, $fields);
            if ($v->validate()) {
                $this->medecinDao->updateMedecin(
                    $this->getData($medecin),
                    $_POST['specialty_id'],
                    $medecin->getUserId(),
                    $medecin->getMedecinId()
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
                //if (count($this->medecinDao->hasChildren($id)) === 0) {
                $this->medecinDao->deleteMedecin($id);
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
