<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\PatientValidator;
use App\Components\Validators\UserValidator;
use App\Dao\PatientDao;
use App\Dao\RoleDao;
use App\Dao\ServiceDao;
use App\Dao\SpecialtyDao;
use App\Dao\UserDao;
use App\Model\CommonModel;
use App\Model\Patient;
use App\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Router;
use App\Router\RouterAction;

class PatientController
{

    private $renderer;
    private $router;
    use RouterAction;
    private $patientDao;
    private $role;
    private $genres = [
        'FEMME' => 'FEMME',
        'HOMME' => 'HOMME'
    ];

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->patientDao = new PatientDao();
        $this->role = (new RoleDao())->listFind('ROLE_PATIENT');
        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'patient');
        $this->router->crud('patient', $this, 'patient');
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
        $patient = new Patient();
        $matricule = $this->patientDao->getMatricule();
        $form = new Form($patient);
        $this->renderer->currentMenu('patient');
        $role = $this->role;
        return $this->renderer->render('@patient/index', compact('role', 'patient', 'form', 'matricule'));
    }

    public function getMatricule()
    {
        $matricule = $this->patientDao->getMatricule();
        return json_encode($matricule, true);
    }

    public function patientFindAll()
    {
        $patients = $this->patientDao->findAllPatient();
        return json_encode(['data' => $patients], true);
    }


    public function new(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $patient = new Patient();
            $patient->setMatricule($this->patientDao->getMatricule());
            $v = new PatientValidator($_POST, (new UserDao()), $patient->getId(), $this->genres);
            $fields = $this->getFields();
            CommonModel::hydrate($patient, $_POST, $fields);
            if ($v->validate()) {
                $idInsert = $this->patientDao->createPatient(
                    $this->getData($patient)
                );
                $patient->getUserId($idInsert);
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
            $patient = $this->patientDao->findbyPatient($id);
            return json_encode($patient, true);
        } else if ($request->getMethod() === 'POST') {
            $errors = [];
            $id = $request->getAttribute('id');
            $patient = $this->patientDao->findbyPatient($id);
            $v = new PatientValidator($_POST, (new UserDao()), $patient->getId(), $this->genres);
            $fields = $this->getFields();
            CommonModel::hydrate($patient, $_POST, $fields);
            if ($v->validate()) {
                $this->patientDao->updatePatient(
                    $this->getData($patient),
                    $patient->getUserId()
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
                //if (count($this->patientDao->hasChildren($id)) === 0) {
                $this->patientDao->deletePatient($id);
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
