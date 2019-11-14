<?php


namespace App;

use App\Session\PHPSession;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    /**
     * @var Router
     */
    private  $router;


    /**
     * @var PHPSession
     */
    private  $session;

    /**
     * @var array
     */
    private $controllers = [];



    public function __construct(array $controllers = [], array $dependencies = [])
    {
        $this->router = new Router();
        $this->session = new PHPSession();
        if (array_key_exists('renderer', $dependencies)) {
            $dependencies['renderer']->addGlobal('router', $this->router);
            $dependencies['renderer']->addGlobal('session', $this->session);
        }
        foreach ($controllers as $controller) {
            $this->controllers[] = new $controller($this->router, $dependencies['renderer'], $this->session);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }

        $route = $this->router->match($request);

        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }

        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $response = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } else if ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('the response is not string or an instance of ResponseInterface');
        }
    }
}
