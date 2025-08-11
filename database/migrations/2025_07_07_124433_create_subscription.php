<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('subscriptions', function (Blueprint $table) {
          $table->id();
          $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
          $table->unsignedInteger('shop_id');
          $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
          $table->dateTime('start_date');
          $table->dateTime('end_date');
          $table->boolean('status')->default(0);
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
        Schema::dropIfExists('subscription');
    }
};
