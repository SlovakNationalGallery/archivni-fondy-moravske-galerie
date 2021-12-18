<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateItems implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('items', function (Mapping $mapping, Settings $settings) {
            $mapping
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
        Index::drop('items');
    }
}
