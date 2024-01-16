<?php

namespace juanignaso\phpmvc\db;

use juanignaso\phpmvc\Application;

class DataBase
{
    #Conexión a la base de datos.
    public \PDO $pdo;

    public function __construct(array $config)
    {
        #dsn -> host,port,database
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Busca dentro del directorio de '/migrations' y aplica las migraciones pendientes a la base de datos
     * configurada en la aplicación.
     */
    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR . '/migrations'); //escanea el directorio en busca del archivo
        $toApplyMigrations = array_diff($files, $appliedMigrations); //coge solo los que están sin aplicar
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }
            #si no, incluir el archivo,crear una instancia y ejecutar el metodo up

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Aplicando Migración $migration");
            $instance->up();
            $this->log("Migración $migration ha sido aplicada");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            //Inserta un nuevo registro dentro de la tabla migraciones si hay alguna migración nueva a aplicar.
            $this->saveMigrations($newMigrations);
        } else {
            $this->log('Todas las Migraciones han sido aplicadas');
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )ENGINE=INNODB;
        ");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(',', array_map(fn($m) => "('$m')", $migrations));

        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");

        $statement->execute();
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }

    public function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}