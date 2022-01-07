<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class AddDefaultAnalyzerToItems implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::pushSettings('items', function (Settings $settings) {
            $settings->analysis([
                'analyzer' => [
                    'default' => [
                        'filter' => [ 'asciifolding' ],
                        'tokenizer' => 'standard',
                    ]
                ]
            ]);
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
