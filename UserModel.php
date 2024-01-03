<?php


namespace juanignaso\phpmvc;

use juanignaso\phpmvc\db\DBmodel;

abstract class UserModel extends DBmodel
{
    abstract public function getUserName(): string;
}