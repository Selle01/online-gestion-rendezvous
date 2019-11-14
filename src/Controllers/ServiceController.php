<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\ServiceValidator;
use App\Dao\ServiceDao;
use App\Model\CommonModel;
use App\Model\Service;
use App\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Router;
use App\Router\RouterAction;
use App\Session\SessionInterface;

class ServiceController
{

    private $renderer;
    private $router;
    private $session;
    use RouterAction;
    private $serviceDao;

    public function __construct(Router $router, RendererInterface $renderer, SessionInterface $session)
    {
        $this->session = $session;
        $this->router = $router;
        $this->renderer = $renderer;
        $this->serviceDao = new ServiceDao();
        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'service');
        $this->router->crud('service', $this, 'service');
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
        $service = new Service();
        $form = new Form($service);
        $this->renderer->currentMenu('service');
        return $this->renderer->render('@service/index', compact('service', 'form'));
    }

    public function serviceFindAll()
    {
        $services = $this->serviceDao->findAll(); //FETCH_ASSOC
        return json_encode(['data' => $services], true);
    }

    public function new(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $service = new Service();
            $v = new ServiceValidator($_POST, $this->serviceDao, $service->getId());
            $fields = ['name', 'created_at'];
            CommonModel::hydrate($service, $_POST, $fields);
            if ($v->validate()) {
                $idInsert =  $this->serviceDao->create([
                    'name' => $service->getName(),
                    'created_at' => $service->getCreatedAt()->format('Y-m-d H:i:s'),
                    'status' => true
                ]);
                $service->setId($idInsert);
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
            $service = $this->serviceDao->find($id);
            return json_encode($service, true);
        } else if ($request->getMethod() === 'POST') {
            $errors = [];
            $id = $request->getAttribute('id');
            $service = $this->serviceDao->find($id);
            $v = new ServiceValidator($_POST, $this->serviceDao, $service->getId());
            $fields = ['name', 'created_at'];
            CommonModel::hydrate($service, $_POST, $fields);
            // dd($service);
            // die();
            if ($v->validate()) {
                $this->serviceDao->update([
                    'name' => $service->getName(),
                    'created_at' => $service->getCreatedAt()->format('Y-m-d H:i:s'),
                ], $service->getId());
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
                if (count($this->serviceDao->hasChildren($id)) === 0) {
                    $this->serviceDao->delete($id);
                    return json_encode(['action' => 'success'], true);
                }
            } catch (\Throwable $th) {
                return json_encode(['action' => 'eroors'], true);
            }
        }
    }

    public function show(ServerRequestInterface $request)
    {
        // $slug = $request->getAttribute('slug');
        // $id = $request->getAttribute('id');
        // $service = $this->serviceDao->find($request->getAttribute('id'));
        // if ($service->getSlug() !==  $slug || $service->getId() !== $id) {
        //     return $this->redirect('service.show', [
        //         'slug' => $service->getSlug(),
        //         'id' => $service->getId()
        //     ]);
        // }
        // return $this->renderer->render('@service/show', [
        //     'service' => $service,
        // ]);
    }
}
