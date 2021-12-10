<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('dating')->nullable();
            $table->integer('date_earliest')->nullable();
            $table->integer('date_latest')->nullable();
            $table->string('part_of_1')->nullable();
            $table->string('part_of_2')->nullable();
            $table->string('institution')->nullable();
            $table->string('archive_fund')->nullable();
            $table->integer('archive_box')->nullable();
            $table->string('archive_folder')->nullable();
            $table->string('archive_file')->nullable();
            $table->json('archive_folder_references')->nullable();
            $table->string('work_type')->nullable();
            $table->timestamps();
            $table->unique(['archive_fund', 'archive_box', 'archive_folder', 'archive_file'], 'archive_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
