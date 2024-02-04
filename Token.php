<?php

namespace juanignaso\phpmvc;

use Application;


class Token
{
    const TABLE_NAME = 'usuariosToken';


    /**
     * Genera tokens aleatorios con un par de selector y validador
     * @return array
     * 
     */
    function generate_tokens(): array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        //selector,validador, pareja de 'selector:validador'
        return [$selector, $validator, $selector . ':' . $validator];
    }

    /**
     * Divide el token almacenado en la cookie ('selector:validador')
     * en selector y validador
     * @return array
     * @return null
     */
    function parsear_token(string $token): ?array
    {
        $parts = explode(':', $token);

        if ($parts && count($parts) == 2) {
            //comprueba que tenga dos partes
            return [$parts[0], $parts[1]];
        }
        //Si no está bien parseada devuelve null
        return null;
    }

    /**
     * Insertar nuevo token en la tabla
     * @param $usuario
     * @param $selector
     * @param $hash_validator
     * @param $fecha_caducidad
     */
    function insertarTokenUsuario($id_usuario, $selector, $hashed_validator, $fecha_caducidad)
    {
        //query
        $statement = self::prepare("INSERT INTO " . self::TABLE_NAME . "(selector,hashed_validator,id_usuario,caducidad) VALUES(:selector,:hashed_validator,:usuario,:fecha_caducidad)");
        $statement->bindValue(":selector", $selector);
        $statement->bindValue(":hashed_validator", $hashed_validator);
        $statement->bindValue(":usuario", $id_usuario);
        $statement->bindValue(":fecha_caducidad", $fecha_caducidad);
        return $statement->execute();
    }

    /**
     * 
     * Find token by a selector
     * La función encuentra una fila en la tabla mediante selector. 
     * solo devuelve el match token si no está caducado comparando la fecha actual
     * con la del match
     * 
     */
    function tokenUsuarioPorSelector(string $selector)
    {

        $sql = 'SELECT id, selector, hashed_validator, id_usuario, caducidad
                FROM ' . self::TABLE_NAME . '
                WHERE selector = :selector AND
                    caducidad >= now()
                LIMIT 1';

        $statement = self::prepare($sql);
        $statement->bindValue(':selector', $selector);

        $statement->execute();

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 
     * Borrar token de usuario, esta función borra todos los tokens asociado con el usuario
     */
    function borrarTokensUsuario(int $id_usuario): bool
    {
        $sql = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE id_usuario = :id_usuario';
        $statement = self::prepare($sql);
        $statement->bindValue(':id_usuario', $id_usuario);

        return $statement->execute();
    }

    /**
     * Encontrar usuario por Token, devuelve id y email
     */
    function encontrarUsrPorToken(string $token)
    {
        $tokens = $this->parsear_token($token);

        if (!$tokens) {
            return null;
        }

        $sql = 'SELECT usuarios.*
            FROM usuarios
            INNER JOIN usuariosToken ON id_usuario = usuarios.id
            WHERE selector = :selector AND
                caducidad > now()
            LIMIT 1';

        $statement = self::prepare($sql);
        $statement->bindValue(':selector', $tokens[0]);
        $statement->execute();

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Comprueba que el token recibido es valido
     * 
     */
    function isTokenValido($token)
    {
        [$selector, $validator] = $this->parsear_token($token);

        $tokens = $this->encontrarUsrPorToken($selector);
        if (!$tokens) {
            return false;
        }
        return password_verify($validator, $tokens['hashed_validator']);
    }


    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function query($sql)
    {
        return Application::$app->db->pdo->query($sql);
    }

}