<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // $table->increments('id')->change();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->char('icon', 100)->nullable();
            $table->string('slug')->nullable();
            $table->text('content')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {

        });
    }
};
