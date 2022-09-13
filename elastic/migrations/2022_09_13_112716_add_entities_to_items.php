<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class AddEntitiesToItems implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::putMapping('items', function (Mapping $mapping) {
            $mapping->keyword('entities');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        //
    }
}
