<?php

namespace app\core;

class View
{
    /**
     * Se mueven todos los métodos de renderizado a una clase View,
     * que se instancia en la Aplicación.
     * 
     */
    public string $title = '';

    /**
     * Renderiza la vista reviendo el nombre de la vista y array de parámetros(opcional).
     * busca el texto {{content}} dentro del div raíz y carga el contenido.
     * 
     * @param string $view
     * @param array $params - Puede ser por ejemplo los resultados de una query recibidos del modelo en el controlador
     */
    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent($viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        $layout = Application::$app->layout;
        #renderiza el layout
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout; // <- Da ERROR cuando se carga una ruta que no existe: Uncaught Error: Typed property app\core\Application::$controller must not be accessed before initialization
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        #para usar las variables dentro del array $params(parámetros)
        /*
        así podremos llamar a la variable:
        así -> $variable
        y no así -> $params['variable']
        */
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        #renderiza la vista
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}