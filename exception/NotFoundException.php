<?php

namespace app\core\exception;

class NotFoundException extends \Exception
{
    protected $message = 'The page youre trying to access doesnt exists.';
    protected $code = 404;
}