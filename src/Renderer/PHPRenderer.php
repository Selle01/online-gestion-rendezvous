<?php

namespace App\Renderer;


class PHPRenderer implements RendererInterface
{
    const DEFAULT_NAMESPACE = '__MAIN__';
    private $paths = [];
    private $globals = [];
    private $globaleMenu = "";
    private $isconnected = false;

    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath(null, $defaultPath);
        }
    }

    public function addPath(?string $path = null, string $namespace): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        $isconnected = $this->isconnected;
        $current_menu = $this->globaleMenu;
        extract($params);
        require($path);
        $content = ob_get_clean();
        return $content;
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        $path =  $this->paths[$namespace] . '/' . (substr($view, 1, strlen($view)));
        //$path = str_replace('@', $namespace, $this->paths[$namespace], $view);
        return $path;
    }

    /**
     * variable accessible pour tout les vues
     *
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    public function currentMenu($value): void
    {
        $this->globaleMenu = $value;
    }

    public function setIsConnected($val)
    {
        $this->isconnected = $val;
    }

    public function getIsConnected()
    {
        return   $this->isconnected;
    }
}
