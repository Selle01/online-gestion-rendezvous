<?php

namespace App;

use App\Router\Route;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

class Router
{
    /**
     *
     * @var FastRouteRouter
     */
    private $fastRouteRouter;

    public function __construct()
    {
        $this->fastRouteRouter = new FastRouteRouter();
    }


    /**
     * Undocumented function
     *
     * @param string $path
     * @param string|callable $callback
     * @param string $name
     * @return void
     */
    public function get(string $path,  $callback, ?string $name = null)
    {
        $this->fastRouteRouter->addRoute(new ZendRoute($path, $callback, ['GET'], $name));
    }

    public function post(string $path,  $callback, ?string $name = null)
    {
        $this->fastRouteRouter->addRoute(new ZendRoute($path, $callback, ['POST'], $name));
    }

    public function both(string $path,  $callback, ?string $name = null)
    {
        $this->fastRouteRouter->addRoute(new ZendRoute($path, $callback, ['GET', 'POST'], $name));
    }

    public function delete(string $path,  $callback, ?string $name = null)
    {
        $this->fastRouteRouter->addRoute(new ZendRoute($path, $callback, ['DELETE'], $name));
    }

    public function crud(string $prefixPath, $thiscallback, string $prefixName)
    {
        $this->get("/$prefixPath", [$thiscallback, 'index'], "$prefixName.index");
        $this->get("/$prefixPath/new/matricule", [$thiscallback, 'getMatricule'], "$prefixName.new.matricule");
        $this->get("/$prefixPath/findAll", [$thiscallback, $prefixName . 'FindAll'], "$prefixName.findAll");
        $this->post("/$prefixPath/new", [$thiscallback, 'new'], "$prefixName.new");
        $this->get("/$prefixPath/{slug:[a-z\-0-9]+}-{id:[0-9]+}/show", [$thiscallback, 'show'], "$prefixName.show");
        $this->both("/$prefixPath/{id:[0-9]+}/edit", [$thiscallback, 'edit'], "$prefixName.edit");
        $this->delete("/$prefixPath/{id:[0-9]+}/delete", [$thiscallback, 'delete'], "$prefixName.delete");
    }


    /**
     * Undocumented function
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result =  $this->fastRouteRouter->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        } else {
            return null;
        }
    }

    public function generateUri(string $name, array $params = []): ?string
    {
        return $this->fastRouteRouter->generateUri($name, $params);
    }
}
