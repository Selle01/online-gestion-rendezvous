<?php

namespace App\Router;


class Route
{

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string|callable
     */
    private $callback;


    /**
     * Undocumented variable
     *
     * @var array
     */
    private $parameters;

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string|callable $callback
     * @param array $parameters
     */
    public function __construct(string $name, $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }


    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return string|callable
     */
    public function getCallback()
    {
        return    $this->callback;
    }

    /**
     * retrieve parameters
     *
     * @return array[]
     */
    public function getParams(): array
    {
        return   $this->parameters;
    }
}
