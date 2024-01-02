<?php

#namespace debe de coincidir con el nombre de la carpeta
#definido en composer.json
namespace app\core;

use app\core\exception\NotFoundException;

/**
 * @package app\core
 */

class Router
{

    public Request $request;

    public Response $response;

    protected array $routes = [];


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Función usada para añadir una ruta usando el protocolo GET, pasando como parámetros una ruta y un callback,
     * dicho callback puede ser un string o un array que contiene el controlador y el método del controlador.
     * 
     * @param string $path
     * @param mixed $callback
     */
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Función usada para añadir una ruta usando el protocolo POST, pasando como parámetros una ruta y un callback,
     * dicho callback puede ser un string o un array que contiene el controlador y el método del controlador.
     * 
     * @param string $path
     * @param mixed $callback
     */
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * Método para resolver una ruta y poder cargar la vista asociada a la url que se está pidiendo
     */
    public function resolve()
    {
        $path = $this->request->getPath(); //<- devuelve la ruta actual
        $method = $this->request->method(); //<- devuelve el protocolo usado en la petición
        $callback = $this->routes[$method][$path] ?? false; //<- archivo php o controlador y método a usar.
        if ($callback === false) {

            throw new NotFoundException();
            return Application::$app->view->renderView("error_page");
        }
        if (is_string($callback)) {
            //Si es un string ej: 'vista.php' llama al método de cargar la vista directamente
            return Application::$app->view->renderView($callback);
        }
        if (is_array($callback)) {
            /*
            En caso de ser array se asume que se está esta llamando a un objeto y por lo tanto
            se crea una instancia de ese objeto
            */

            /** @var \app\core\Controller $controller  */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            //iterar sobre los middleware
            foreach ($controller->getMiddleWares() as $middleware) {
                $middleware->execute(); //El middleware lanzará una excepción si algo mal ocurre
            }
        }

        return call_user_func($callback, $this->request, $this->response); //<- se está llamando a la función???
    }

}
?>