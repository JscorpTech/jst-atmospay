<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JscorpTech\Atmospay\Enums\StatusEnum;

return new class extends Migration {

    public function up()
    {
        Schema::create("atmospay_cards", function (Blueprint $table) {
            $table->id();
            $table->string("number");
            $table->string("expiry");
            $table->string("token")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table("atmospay_cards", function (Blueprint $table) {
            $table->dropIfExists("atmospay_cards");
        });
    }
};
