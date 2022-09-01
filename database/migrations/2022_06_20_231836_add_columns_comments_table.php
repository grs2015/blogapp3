<?php

use App\Models\Comment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->increments('id')->change();
            $table->string('title');
            $table->text('content')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->char('status', 100)->nullable();
            $table->string('author')->nullable();

            $table->foreignId('post_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {

        });
    }
};
