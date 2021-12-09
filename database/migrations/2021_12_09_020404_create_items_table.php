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
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('inventory_number_mg')->nullable();
            $table->string('dating')->nullable();
            $table->integer('date_earliest')->nullable();
            $table->integer('date_latest')->nullable();
            $table->string('author_image')->nullable();
            $table->string('part_of_1')->nullable();
            $table->string('part_of_2')->nullable();
            $table->string('institution')->nullable();
            $table->string('archive_fund');
            $table->string('collection')->nullable();
            $table->string('inventory_number')->nullable();
            $table->string('archive_box')->nullable();
            $table->string('archive_folder')->nullable();
            $table->string('work_type')->nullable();
            $table->string('related_item')->nullable();
            $table->string('related_entity')->nullable();
            $table->timestamps();
            $table->unique(['archive_fund', 'inventory_number']);
            $table->unique(['archive_fund', 'archive_box', 'archive_folder']);
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
