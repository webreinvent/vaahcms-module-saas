<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class V3SaasServer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('v3-saas-server')) {
            Schema::create('v3-saas-server', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned();
                $table->uuid('uuid')->nullable()->index();

                $table->string('name')->nullable()->index();
                $table->string('slug')->nullable()->index();
                $table->boolean('is_active')->nullable()->index();

                $table->string('host_type')->nullable(); //cPanel | mysql

                $table->string('driver')->nullable(); // mysql | redis
                $table->string('host')->nullable()->index();
                $table->string('port')->nullable();
                $table->string('username')->nullable();
                $table->string('password',255)->nullable();
                $table->string('sslmode')->nullable();

                $table->integer('count_tenants')->nullable(); // count database in the server
                $table->integer('count_db_instances')->nullable(); // count database in the server
                $table->dateTime('activated_at')->nullable();

                //----common fields
                $table->text('meta')->nullable();
                $table->bigInteger('created_by')->nullable()->index();
                $table->bigInteger('updated_by')->nullable()->index();
                $table->bigInteger('deleted_by')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['created_at', 'updated_at', 'deleted_at']);
                //----/common fields

            });
        }

    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('v3-saas-server');
    }
}
