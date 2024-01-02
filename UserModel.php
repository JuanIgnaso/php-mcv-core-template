<?php


namespace app\core;

use app\core\db\DBmodel;

abstract class UserModel extends DBmodel
{
    abstract public function getUserName(): string;
}