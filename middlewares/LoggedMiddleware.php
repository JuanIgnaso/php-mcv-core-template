<?php

namespace juanignaso\phpmvc\middlewares;

use juanignaso\phpmvc\Application;
use juanignaso\phpmvc\exception\NotFoundException;

class LoggedMiddleware extends BaseMiddleware
{
    public array $actions = [];

    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (!Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new NotFoundException();
            }
        }
    }
}