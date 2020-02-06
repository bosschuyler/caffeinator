<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_system_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('status');
            $table->string('password_expires_at')->nullable();

            $table->timestamps();
        });

        Schema::create('app_system_email_address', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->boolean('valid')->nullable();
            $table->boolean('disposible')->nullable();
            $table->boolean('found')->nullable();
            $table->string('verification')->nullable();
            $table->datetime('validated_at')->nullable();

            $table->timestamps();
        });

        Schema::create('app_system_api_key', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('app_system_user_email_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name')->nullable();
            $table->boolean('primary');
            $table->integer('email_address_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id', 'app_email_user_id_foreign')->references('id')->on('app_system_user')->onDelete('cascade');
            $table->foreign('email_address_id', 'app_email_email_address_id_foreign')->references('id')->on('app_system_email_address')->onDelete('cascade');
        });

        Schema::create('app_system_api_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('api_key_id')->unsigned();

            $table->foreign('user_id', 'app_api_user_id_foreign')->references('id')->on('app_system_user')->onDelete('cascade');
            $table->foreign('api_key_id', 'app_api_api_key_id_foreign')->references('id')->on('app_system_api_key')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_system_user_email_addresses', function(Blueprint $table) {
            $table->dropForeign('app_email_user_id_foreign');
            $table->dropForeign('app_email_email_address_id_foreign');
        });

        Schema::table('app_system_api_user', function(Blueprint $table) {
            $table->dropForeign('app_api_user_id_foreign');
            $table->dropForeign('app_api_api_key_id_foreign');
        });

        Schema::dropIfExists('app_system_api_key');
        Schema::dropIfExists('app_system_api_user');
        Schema::dropIfExists('app_system_user_email_addresses');
        Schema::dropIfExists('app_system_email_address');
        Schema::dropIfExists('app_system_user');
    }
}
