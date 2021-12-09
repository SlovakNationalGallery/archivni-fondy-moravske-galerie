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
            $mapping->keyword('author')
                ->keyword('author_image')
                ->keyword('part_of_1')
                ->keyword('part_of_2')
                ->keyword('institution')
                ->keyword('archive_fund')
                ->keyword('collection')
                ->keyword('archive_box')
                ->keyword('archive_folder')
                ->keyword('work_type')
                ->keyword('related_item')
                ->keyword('related_entity')
                ->integer('date_earliest')
                ->integer('date_latest');
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
