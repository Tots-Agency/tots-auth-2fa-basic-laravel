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
        Schema::create('tots_user_code', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->string('sent', 250)->nullable(true);
            $table->string('code', 10)->index()->nullable(false);
            $table->tinyInteger('provider')->nullable(false)->default(0)->comment('0 = Email');
            $table->tinyInteger('status')->nullable(false)->default(0)->comments('0 = Pending, 1 = Verified, 2 = Used, 3 = Expired');
            $table->dateTime('expired_at')->nullable(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tots_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tots_user_code');
    }
};
