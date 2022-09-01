<?php

use App\Models\Post;
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
        Schema::table('posts', function (Blueprint $table) {
            // $table->increments('id')->change();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->string('slug')->nullable();
            // $table->text('summary')->nullable()->fulltext();
            $table->text('summary')->nullable();
            $table->char('published', 100)->nullable();
            $table->char('status', 100)->nullable();
            $table->dateTime('published_at')->nullable();
            // $table->text('content')->nullable()->fulltext();
            $table->text('content')->nullable();
            $table->unsignedInteger('views')->nullable()->default(0);
            $table->string('hero_image')->nullable();
            $table->string('images')->nullable();
            $table->unsignedSmallInteger('time_to_read')->nullable();
            $table->char('favorite', 100)->nullable();

            $table->foreignId('author_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

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
        Schema::table('posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
