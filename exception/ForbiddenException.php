<?php

namespace juanignaso\phpmvc\exception;

class ForbiddenException extends \Exception
{
    protected $message = 'You dont have permission to access here.';
    protected $code = 403;
}