<?php

$isDdev = getenv('IS_DDEV_PROJECT') === 'true';

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf(
        'mysql:host=%s;dbname=%s',
        getenv('DB_HOST') ?: ($isDdev ? 'db' : 'localhost'),
        getenv('DB_NAME') ?: ($isDdev ? 'StudyOrganiser' : 'yii2basic')
    ),
    'username' => getenv('DB_USER') ?: ($isDdev ? 'root' : 'root'),
    'password' => getenv('DB_PASSWORD') ?: ($isDdev ? 'root' : ''),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
