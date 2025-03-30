<?php

namespace Grimzy\LaravelMysqlSpatial;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Grimzy\LaravelMysqlSpatial\Schema\Builder;
use Grimzy\LaravelMysqlSpatial\Schema\Grammars\MySqlGrammar;
use Illuminate\Database\MySqlConnection as IlluminateMySqlConnection;
use PDO;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Closure;
use Doctrine\DBAL\Driver\PDO\Connection;

class MysqlConnection extends IlluminateMySqlConnection
{
    public function __construct(PDO|Closure $pdo, string $database = '', string $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);

        if (class_exists(DoctrineType::class)) {
            // Prevent geometry type fields from throwing a 'type not found' error when changing them
            $geometries = [
                'geometry',
                'point',
                'linestring',
                'polygon',
                'multipoint',
                'multilinestring',
                'multipolygon',
                'geometrycollection',
                'geomcollection',
            ];
            
            if ($pdo instanceof PDO) {
                $connection = new Connection($pdo);
                $dbPlatform = $connection->getDatabasePlatform();
                foreach ($geometries as $type) {
                    $dbPlatform->registerDoctrineTypeMapping($type, 'string');
                }
            }
        }
    }

    /**
     * Get the default schema grammar instance.
     */
    protected function getDefaultSchemaGrammar(): MySqlGrammar
    {
        return new MySqlGrammar();
    }

    /**
     * Get a schema builder instance for the connection.
     */
    public function getSchemaBuilder(): SchemaBuilder
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new Builder($this);
    }
}
