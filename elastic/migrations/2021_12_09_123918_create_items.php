<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateItems implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('items', function (Mapping $mapping) {
            $mapping
                ->text('title')
                ->text('description')
                ->keyword('authors')
                ->keyword('part_of')
                ->keyword('institution')
                ->keyword('archive_fund')
                ->keyword('archive_box')
                ->keyword('archive_folder')
                ->keyword('work_type')
                ->integer('date_earliest')
                ->integer('date_latest')
                ->keyword('images');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('items');
    }
}
