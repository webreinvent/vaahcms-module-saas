<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VhSaasApps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vh_saas_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable()->index();
            $table->string('app_type')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('excerpt')->nullable();

            $table->string('version')->nullable();
            $table->integer('version_number')->nullable();

            $table->string('relative_path')->nullable();
            $table->string('migration_path')->nullable();
            $table->string('seed_class')->nullable();
            $table->string('sample_data_class')->nullable();

            $table->dateTime('activated_at')->nullable();
            $table->boolean('is_active')->nullable();

            $table->dateTime('is_deactivated_at')->nullable();
            $table->string('notes')->nullable();

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
        Schema::dropIfExists('vh_saas_apps');
    }
}
