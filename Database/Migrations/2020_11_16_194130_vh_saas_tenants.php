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
            $table->uuid('uuid')->nullable();
            $table->integer('vh_saas_server_id')->nullable();

            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('path')->nullable();
            $table->string('domain')->nullable();
            $table->string('sub_domain')->nullable();

            $table->string('database_name')->nullable();
            $table->string('database_charset')->nullable()->default('utf8mb4');
            $table->string('database_collation')->nullable()->default('utf8mb4_unicode_ci');
            $table->dateTime('is_database_created_at')->nullable();
            $table->dateTime('is_active_at')->nullable();
            $table->dateTime('is_deactivated_at')->nullable();
            $table->string('notes')->nullable();

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
