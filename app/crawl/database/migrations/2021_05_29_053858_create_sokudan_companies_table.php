<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSokudanCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sokudan_companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->unique();
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->longText('description')->nullable();
            $table->string('founding_date')->nullable();
            $table->string('employees')->nullable();
            $table->string('address')->nullable();
            $table->string('evaluation')->nullable();
            $table->string('evaluation_order')->nullable();
            $table->string('evaluation_communication')->nullable();
            $table->string('evaluation_schedule')->nullable();
            $table->string('evaluation_budget_setting')->nullable();
            $table->string('evaluation_compliance')->nullable();
            $table->string('evaluation_coordination')->nullable();
            $table->string('evaluation_honesty')->nullable();
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
        Schema::dropIfExists('sokudan_companies');
    }
}
