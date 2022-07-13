<?php

use App\Models\User;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->char('mobile', 100)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->text('intro')->nullable();
            $table->text('profile')->nullable();
            $table->string('avatar')->nullable();
            $table->char('status', 100)->default(User::PENDING);

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
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
