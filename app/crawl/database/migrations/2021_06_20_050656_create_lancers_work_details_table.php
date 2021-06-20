<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLancersWorkDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lancers_work_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('work_id');
            $table->text('subject')->nullable();
            $table->text('ordering_party')->nullable();
            $table->text('ai_judgment')->nullable();
            $table->text('style')->nullable();
            $table->text('price')->nullable();
            $table->text('remaining_time')->nullable();
            $table->text('number_of_proposals')->nullable();
            $table->text('favorite')->nullable();
            $table->text('number_of_views')->nullable();
            $table->text('remark_1')->nullable();
            $table->text('remark_2')->nullable();
            $table->text('start_time')->nullable();
            $table->text('end_time')->nullable();
            $table->text('logo_notation_name')->nullable();
            $table->longText('features')->nullable();
            $table->text('desired_logo_type')->nullable();
            $table->longText('hope_image')->nullable();
            $table->text('desired_color')->nullable();
            $table->text('trademark')->nullable();
            $table->text('usage')->nullable();
            $table->text('delivery_file')->nullable();
            $table->text('supplementary')->nullable();
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
        Schema::dropIfExists('lancers_work_details');
    }
}
