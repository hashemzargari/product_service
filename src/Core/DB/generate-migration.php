<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Hertz\ProductService\Core\DB\MigrationGenerator;
use Hertz\ProductService\Core\DB\BaseEntity;

if ($argc < 2) {
    echo "Usage: php generate-migration.php <EntityClassName>\n";
    echo "Example: php generate-migration.php ProductEntity\n";
    exit(1);
}

$entityClass = $argv[1];
$fullClassName = "Hertz\\ProductService\\Entity\\{$entityClass}";

// Debug information
echo "Checking BaseEntity class:\n";
echo "BaseEntity exists: " . (class_exists(BaseEntity::class) ? 'yes' : 'no') . "\n";
echo "BaseEntity location: " . (new ReflectionClass(BaseEntity::class))->getFileName() . "\n\n";

// Load the entity class file
$entityFile = __DIR__ . "/../../Entity/{$entityClass}.php";
if (!file_exists($entityFile)) {
    echo "Error: Entity file not found at {$entityFile}\n";
    exit(1);
}

require_once $entityFile;

try {
    if (!class_exists($fullClassName)) {
        echo "Error: Class {$fullClassName} not found\n";
        exit(1);
    }

    // Debug information about the entity class
    echo "Checking ProductEntity class:\n";
    echo "ProductEntity exists: " . (class_exists($fullClassName) ? 'yes' : 'no') . "\n";
    echo "ProductEntity location: " . (new ReflectionClass($fullClassName))->getFileName() . "\n";
    echo "ProductEntity parent: " . (get_parent_class($fullClassName) ?: 'none') . "\n\n";

    $migrationContent = MigrationGenerator::generateMigration($fullClassName);

    // Generate timestamp for migration filename
    $timestamp = date('YmdHis');
    $filename = __DIR__ . "/../../../database/migrations/{$timestamp}_create_" . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $entityClass)) . "_table.php";

    file_put_contents($filename, $migrationContent);
    echo "Migration file created: {$filename}\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}