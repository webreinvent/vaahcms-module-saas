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
            $table->uuid('uuid')->nullable();

            $table->string('name')->nullable(); // server label
            $table->string('slug')->nullable(); // server label

            $table->string('hosted_by')->nullable(); //cPanel | mysql

            $table->string('driver')->nullable(); // mysql | redis
            $table->string('host')->nullable();
            $table->string('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->integer('count_instances')->nullable(); // count database in the server
            $table->dateTime('is_active_at')->nullable();
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
