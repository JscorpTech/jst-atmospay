<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JscorpTech\Atmospay\Enums\StatusEnum;

return new class extends Migration {

    public function up()
    {
        Schema::create("atmospay_transactions", function (Blueprint $table) {
            $table->id();
            $table->decimal("amount", 20, 2);
            $table->enum("status", ["pending", "cancel", "done"])->default("pending");
            $table->bigInteger("account");
            $table->bigInteger("transaction_id");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table("atmospay_transactions", function (Blueprint $table) {
            $table->dropIfExists("atmospy_transactions");
        });
    }
};
