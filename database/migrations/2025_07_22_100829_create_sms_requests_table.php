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
        Schema::create('sms_requests', function (Blueprint $table) {
            $table->id();
       		  $table->unsignedInteger('shop_id');
   			 $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->string('phone');
            $table->text('message');
            $table->enum('status', ['sent', 'pending', 'rejected'])->default('pending');
            
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
        Schema::dropIfExists('sms_requests');
    }
};
