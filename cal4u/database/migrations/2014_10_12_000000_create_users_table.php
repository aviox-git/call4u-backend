<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('facebook_id')->nullable();
            $table->string('googleplus_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('country_code')->nullable();
            $table->string('mobile')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 12, 8)->nullable();
            $table->enum('status', ['0', '1'])->default('1');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
