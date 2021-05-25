<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLancersClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lancers_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('client_id')->unique();
            $table->string('client_type')->nullable();
            $table->string('client_city')->nullable();
            $table->string('client_status')->nullable();
            $table->string('client_industry')->nullable();
            $table->string('client_identification')->nullable()->comment('本人確認');
            $table->string('client_confidentiality_confirmation')->nullable()->comment('機密保持確認');
            $table->string('client_phone_confirmation')->nullable()->comment('電話確認');
            $table->string('client_lancers_check')->nullable()->comment('ランサーズチェック');
            $table->string('client_register_date')->nullable();
            $table->string('client_number_order')->nullable();
            $table->string('client_evaluation')->nullable();
            $table->string('client_order_rate')->nullable();
            $table->string('client_order_rate_note')->nullable();
            $table->longText('client_description')->nullable();
            $table->longText('client_about')->nullable();
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
        Schema::dropIfExists('lancers_clients');
    }
}
