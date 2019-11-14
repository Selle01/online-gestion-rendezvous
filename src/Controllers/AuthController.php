<?php

namespace App\Controllers;

use App\Components\Forms\Form;
use App\Components\Validators\AuthValidator;
use App\Dao\AuthDao;
use Psr\Http\Message\ServerRequestInterface;
use App\Dao\MedecinDao;
use App\Model\CommonModel;
use App\Model\User;
use App\Renderer\RendererInterface;
use App\Router;
use App\Router\RouterAction;
use App\Session\SessionInterface;
use GuzzleHttp\Psr7\Response;

class AuthController
{
    private $renderer;
    private $router;
    private $session;
    use RouterAction;


    public function __construct(Router $router, RendererInterface $renderer, SessionInterface $session)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->medecinDao = new MedecinDao();
        $this->session = $session;
        $this->renderer->addPath(dirname(dirname(__DIR__)) . '/views', 'login');
        $this->router->get('/login', [$this, 'index'], 'login.index');
        $this->router->post('/login/auth', [$this, 'login'], 'login.auth');
        $this->router->post('/login', [$this, 'logout'], 'login.out');
    }


    public function index()
    {
        $user = new User();
        $form = new Form($user);
        return $this->renderer->render('@login/login', compact('form'));
    }

    public function login(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $errors = [];
            $user = new User();
            $authDao = new AuthDao($this->session);
            $v = new AuthValidator($_POST);
            $fields = ['login', 'password'];
            CommonModel::hydrate($user, $_POST, $fields);
            if ($v->validate()) {
                $user = $authDao->login([
                    'login' => $_POST['login'],
                    'password' => $_POST['password'],
                ]);
                if (!is_null($user)) {
                    //$this->renderer->setIsConnected(true);
                    $role = $this->session->get('auth.user')->getRole()->getTitle();
                    $this->renderer->addGlobal('role', $role);
                    $this->renderer->addGlobal('isConnected', true);
                    if ($role === "ROLE_MEDECIN") {
                        $redirectUri = $this->router->generateUri('rendezVous.index');
                    } else  if ($role === "ROLE_SECRETARY") {
                        $redirectUri = $this->router->generateUri('rendezVous.index');
                    }
                    return (new Response())
                        ->withStatus(200)
                        ->withHeader('Location', $redirectUri);
                } else {
                    $redirectUri = $this->router->generateUri('login.index');
                    return (new Response())
                        ->withStatus(301)
                        ->withHeader('Location', $redirectUri);
                }
            } else {
                $errors[] = $v->errors();
                return json_encode(['action' => 'error', 'errors' => $errors], true);
            }
        }
    }
    public function logout()
    {
        $this->renderer->setIsConnected(false);
        $this->session->delete('auth.user');
        $redirectUri = $this->router->generateUri('login.index');
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
