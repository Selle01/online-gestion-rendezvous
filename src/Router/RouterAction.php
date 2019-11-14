<?php

namespace App\Router;

use App\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait RouterAction
{
    private $router = null;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }

    public function isConnected($etat)
    {
        // if ($etat === false) {
        //     return (new Response())
        //         ->withStatus(301)
        //         ->withHeader('Location',  $this->router->generateUri('login.index'));
        //     exit();
        // }
        // dd($this->renderer->getIsConnected());
        // if ($this->renderer->getIsConnected() == false) {
        //     $this->router->generateUri('login.index');
        // }
    }
}
