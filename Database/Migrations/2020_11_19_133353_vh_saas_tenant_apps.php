<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VhSaasTenantApps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vh_saas_tenant_apps', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('vh_saas_tenant_id')->nullable()->index();
            $table->integer('vh_saas_app_id')->nullable()->index();

            $table->string('version')->nullable();
            $table->integer('version_number')->nullable();

            $table->boolean('is_active')->nullable();

            $table->dateTime('last_migrated_at')->nullable();
            $table->dateTime('last_seeded_at')->nullable();

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
        Schema::dropIfExists('vh_saas_tenant_apps');
    }
}
