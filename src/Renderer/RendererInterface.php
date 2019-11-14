<?php

namespace App\Renderer;


interface RendererInterface
{

    public function addPath(?string $path = null, string $namespace): void;

    public function render(string $view, array $params = []): string;

    public function addGlobal(string $key, $value): void;
}
