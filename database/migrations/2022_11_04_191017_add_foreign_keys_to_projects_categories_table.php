<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProjectsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects_categories', function (Blueprint $table) {
            $table->foreign(['project_id'], 'projects_categories_ibfk_1')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['category_id'], 'projects_categories_ibfk_2')->references(['id'])->on('categories')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects_categories', function (Blueprint $table) {
            $table->dropForeign('projects_categories_ibfk_1');
            $table->dropForeign('projects_categories_ibfk_2');
        });
    }
}
