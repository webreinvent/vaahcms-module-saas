<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VhSaasTenants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vh_saas_tenants', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable()->index();

            $table->integer('vh_saas_server_id')->nullable()->index();




            $table->string('name')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->string('path')->nullable()->index();
            $table->string('domain')->nullable()->index();
            $table->string('sub_domain')->nullable()->index();

            $table->integer('count_apps_active')->nullable();
            $table->integer('count_apps')->nullable();

            $table->string('database_name')->nullable();
            $table->string('database_username')->nullable();
            $table->mediumText('database_password')->nullable();
            $table->string('database_sslmode')->nullable();

            $table->string('database_charset')->nullable()->default('utf8mb4');
            $table->string('database_collation')->nullable()->default('utf8mb4_unicode_ci');


            $table->dateTime('is_database_created_at')->nullable();
            $table->dateTime('is_database_user_created_at')->nullable();
            $table->dateTime('is_database_user_assigned_at')->nullable();

            $table->dateTime('activated_at')->nullable();
            $table->boolean('is_active')->nullable();
            $table->dateTime('is_deactivated_at')->nullable();
            $table->string('notes',250)->nullable();

            $table->text('meta')->nullable();

            // COMMON FIELDS
            $table->integer('created_by')->nullable();

            $table->integer('updated_by')->nullable();

            $table->integer('deleted_by')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('vh_saas_tenants');
    }
}
