<?php

namespace app\core;

use app\core\middlewares\BaseMiddleware;

class Controller
{

    public string $layout = 'main'; //<- layout por defecto.
    public string $action = '';
    protected array $middlewares = []; //array de clases middlewares

    /**
     * Define un nuevo layout para cargar la vista.
     * @param string $layout - nombre del nuevo layout.
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Llama a la función renderView() del router para cargar la vista.
     * 
     * @see Router - renderView
     */
    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddleWares(): array
    {
        return $this->middlewares;
    }
}