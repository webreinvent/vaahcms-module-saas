<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VhSaasServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vh_saas_servers', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable()->index();

            $table->string('name')->nullable()->index(); // server label
            $table->string('slug')->nullable()->index(); // server label

            $table->string('host_type')->nullable(); //cPanel | mysql

            $table->string('driver')->nullable(); // mysql | redis
            $table->string('host')->nullable()->index();
            $table->string('port')->nullable();
            $table->string('username')->nullable();
            $table->mediumText('password')->nullable();

            $table->integer('count_tenants')->nullable(); // count database in the server
            $table->integer('count_db_instances')->nullable(); // count database in the server
            $table->dateTime('activated_at')->nullable();
            $table->boolean('is_active')->nullable();
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
        Schema::dropIfExists('vh_saas_servers');
    }
}
