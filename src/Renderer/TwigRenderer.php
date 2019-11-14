<?php

namespace App\Renderer;


class TwigRenderer implements RendererInterface
{
    private $twig;
    private $loader;

    public function __construct(string $path)
    {

        $this->loader = new \Twig_Loader_Filesystem($path);
        $this->twig = new \Twig_Environment($this->loader, []);
        // var_dump($this->loader);
        // die();
    }

    public function addPath(?string $path = null, string $namespace): void
    {
        $this->loader->addPath($path, $namespace);
    }

    public function render(string $view, array $params = []): string
    {
        return  $this->twig->render(substr($view, 1, strlen($view)) . '.twig', $params);
    }

    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
