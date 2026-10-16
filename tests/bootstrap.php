<?php
declare(strict_types=1);

/**
 * Test suite bootstrap for QueryFilter.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);

require_once $root . '/vendor/autoload.php';

/**
 * Define fallback values for required constants and configuration.
 * To customize constants and configuration remove this require
 * and define the data required by your plugin here.
 */
require_once $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

if (file_exists($root . '/config/bootstrap.php')) {
    require $root . '/config/bootstrap.php';

    return;
}

use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\Fixture\SchemaLoader;

// The plugin tests are self-contained: use an in-memory SQLite database.
if (!ConnectionManager::getConfig('test')) {
    ConnectionManager::setConfig('test', ['url' => 'sqlite:///:memory:']);
}

// Create the schema here; fixtures are data-only.
(new SchemaLoader())->loadSqlFiles('tests/schema.sql', 'test');
