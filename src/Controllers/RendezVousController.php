<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\RendezVousValidator;
use App\Dao\RendezVousDao;
use App\Model\CommonModel;
use App\Model\Rendezvous;
use App\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Router;
use App\Router\RouterAction;
use App\Session\SessionInterface;

class RendezVousController
{

    private $renderer;
    private $router;
    private $rendezVousDao;
    use RouterAction;
    private $session;
    private $role_id;
    private $service_id;
    private $specialty_id;

    private $patients;
    private $medecins;
    private $secretaries;

    private $intervalles = [
        '8h00mn-8h15mn' => '8h00mn-8h15mn',
        '8h15mn-8h30mn' => '8h15mn-8h30mn',
        '8h30mn-8h45mn' => '8h30mn-8h45mn',
        '8h45mn-9h00mn' => '8h45mn-9h00mn',
        '9h00mn-9h15mn' => '9h00mn-9h15mn',
        '9h15mn-9h30mn' => '9h15mn-9h30mn',
    ];


    public function __construct(Router $router, RendererInterface $renderer, SessionInterface $session)
    {

        $this->router = $router;
        $this->renderer = $renderer;
        $this->session = $session;
        $this->rendezVousDao = new RendezVousDao();
        $this->patients = $this->rendezVousDao->listofPatient();

        $this->service_id = method_exists($this->session->get('auth.user'), "getServiceId") ? $this->session->get('auth.user')->getServiceId() : null;
        $this->specialty_id =  method_exists($this->session->get('auth.user'), "getSpecialtyId")  ? $this->session->get('auth.user')->getSpecialtyId() : null;
        //dd($this->session->get('auth.user'));
        if (method_exists($this->session->get('auth.user'), "getServiceId")) {
            $this->secretaries =  $this->rendezVousDao->listofSecretaryByService($this->service_id, $this->session->get('auth.user')->getSecretaryId());
            $this->medecins =  $this->rendezVousDao->listofMedecinByService($this->service_id);
        } else {
            // $this->secretaries =  $this->rendezVousDao->listofSecretaryByService($this->service_id);
            // $this->medecins =  $this->rendezVousDao->listofMedecinByService($this->service_id);
        }

        $this->role_id = $this->session->get('auth.user') !== null ? $this->session->get('auth.user')->getRoleId() : null;

        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'rendezVous');
        $this->router->crud('rendezVous', $this, 'rendezVous');
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

        $rendezVous = new Rendezvous();
        $form = new Form($rendezVous);
        $medecins =  $this->medecins;
        $patients =  $this->patients;
        $secretaries =   $this->secretaries;
        $this->renderer->currentMenu('rendezVous');
        return $this->renderer->render('@rendezVous/index', compact('rendezVous', 'medecins', 'patients', 'secretaries', 'form'));
    }
    public function rendezVousFindAll()
    {
        $rendezvous = $this->rendezVousDao->findAllRendezVous();
        //dd($rendezvous);
        return json_encode(['data' => $rendezvous], true);
    }


    public function new(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $rendezvous = new Rendezvous();
            $medecins =  $this->medecins;
            $patients =  $this->patients;
            $secretaries = $this->secretaries;
            $v = new RendezVousValidator($_POST, $this->rendezVousDao, $rendezvous->getRVId(), $this->intervalles, $medecins, $patients, $secretaries);
            $fields = $this->getFields();
            CommonModel::hydrate($rendezvous, $_POST, $fields);
            if ($v->validate()) {
                $this->rendezVousDao->createRendezVous(
                    $this->getData($rendezvous)
                );
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
            $rendezvous = $this->rendezVousDao->findbyRendezVous($id);
            // $this->rendezVousDao->hydrateSecretary($secretary);
            return json_encode($rendezvous, true);
        } else
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $id = (int) $request->getAttribute('id');
            $rendezvous = $this->rendezVousDao->findbyRendezVous($id);
            $medecins =  $this->medecins;
            $patients =  $this->patients;
            $secretaries = $this->secretaries;
            $v = new RendezVousValidator($_POST, $this->rendezVousDao, $rendezvous->getRVId(), $this->intervalles, $medecins, $patients, $secretaries);
            $fields = $this->getFields();
            CommonModel::hydrate($rendezvous, $_POST, $fields);
            if ($v->validate()) {
                $this->rendezVousDao->updateRendezVous(
                    $this->getData($rendezvous)
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
                $this->rendezVousDao->deleteRendezVous($id);
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
            'medecin_id',
            'secretary_id',
            'patient_id',
            'heure_rv',
            'date_rv'
        ];
    }

    private function getData($item)
    {
        return   [
            'rv_id' => $item->getRvId(),
            'medecin_id' => $item->getMedecinId(),
            'secretary_id' => $item->getSecretaryId(),
            'patient_id' => $item->getPatientId(),
            'status' => $item->getStatus(),
            'heure_rv' => $item->getHeureRV(),
            'date_rv' =>  $item->getDateRV()->format('Y-m-d H:i:s'),
        ];
    }
}
