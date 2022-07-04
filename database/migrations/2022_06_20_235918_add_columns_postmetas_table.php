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
        Schema::table('postmetas', function (Blueprint $table) {
            $table->increments('id')->change();
            $table->string('key')->nullable();
            $table->string('content')->nullable();

            $table->foreignId('post_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postmetas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
