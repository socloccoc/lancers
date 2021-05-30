<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrowdworksClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crowdworks_clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->unique();
            $table->string('name')->nullable();
            $table->string('evaluation')->nullable();
            $table->string('evaluation_count')->nullable();
            $table->string('job_offer_achievement_count')->nullable();
            $table->string('project_completion_rate')->nullable();
            $table->string('total_finished_count')->nullable();
            $table->string('total_acceptance_count')->nullable();
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
        Schema::dropIfExists('crowdworks_clients');
    }
}
