<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\SpecialtyValidator;
use App\Dao\ServiceDao;
use App\Dao\SpecialtyDao;
use App\Model\CommonModel;
use App\Model\Specialty;
use App\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Router;
use App\Router\RouterAction;

class SpecialtyController
{

    private $renderer;
    private $router;
    use RouterAction;
    private $specialtyDao;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->specialtyDao = new SpecialtyDao();
        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'specialty');
        $this->router->crud('specialty', $this, 'specialty');
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
        $specialty = new Specialty();
        $form = new Form($specialty);
        $services = (new ServiceDao())->list();
        $this->renderer->currentMenu('specialty');
        return $this->renderer->render('@specialty/index', compact('services', 'specialty', 'form'));
    }

    public function specialtyFindAll()
    {
        $specialties = $this->specialtyDao->findAllSpecialty();
        return json_encode(['data' => $specialties], true);
    }


    public function new(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $specialty = new Specialty();
            $services = (new ServiceDao())->list();
            $v = new SpecialtyValidator($_POST, $this->specialtyDao, $specialty->getId(), $services);
            $fields = ['name', 'created_at', 'service_id'];
            CommonModel::hydrate($specialty, $_POST, $fields);
            if ($v->validate()) {
                $idInsert =  $this->specialtyDao->create([
                    'name' => $specialty->getName(),
                    'created_at' => $specialty->getCreatedAt()->format('Y-m-d H:i:s'),
                    'service_id' => $specialty->getServiceId(),
                    'status' => true
                ]);
                $specialty->setId($idInsert);
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
            $specialty = $this->specialtyDao->find($id);
            $this->specialtyDao->hydrateSpecialty($specialty);
            return json_encode($specialty, true);
        } else if ($request->getMethod() === 'POST') {
            $errors = [];
            $id = $request->getAttribute('id');
            $specialty = $this->specialtyDao->find($id);
            $services = (new ServiceDao())->list();
            $v = new SpecialtyValidator($_POST, $this->specialtyDao, $specialty->getId(), $services);
            $fields = ['name', 'created_at', 'service_id'];
            CommonModel::hydrate($specialty, $_POST, $fields);
            if ($v->validate()) {
                $this->specialtyDao->update([
                    'name' => $specialty->getName(),
                    'service_id' => $specialty->getServiceId(),
                    'created_at' => $specialty->getCreatedAt()->format('Y-m-d H:i:s'),
                ], $specialty->getId());
                return json_encode(['action' => 'success'], true);
            } else {
                $errors = $v->errors();
                return json_encode(['action' => 'error', 'errors' => $errors], true);
            }
        }
    }

    public function delete(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'DELETE') {
            try {
                $id = $request->getAttribute('id');
                //if (count($this->specialtyDao->hasChildren($id)) === 0) {
                $this->specialtyDao->delete($id);
                return json_encode(['action' => 'success'], true);
                exit();
                // }
            } catch (\Throwable $th) {
                return json_encode(['action' => 'eroors'], true);
            }
        }
    }

    public function show(ServerRequestInterface $request)
    {
        //     $slug = $request->getAttribute('slug');
        //     $id = $request->getAttribute('id');
        //     $specialty = $this->specialtyDao->find($request->getAttribute('id'));
        //     if ($specialty->getSlug() !==  $slug || $specialty->getId() !== $id) {
        //         return $this->redirect('specialty.show', [
        //             'slug' => $specialty->getSlug(),
        //             'id' => $specialty->getId()
        //         ]);
        //     }
        //     return $this->renderer->render('@specialty/show', [
        //         'specialty' => $specialty,
        //     ]);
    }
}
