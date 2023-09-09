<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('initiated_by');
            $table->string('txn_id');
            $table->text('receiver_name')->nullable();
            $table->bigInteger('receiver_account_no');
            $table->integer('amount');
            $table->string('currency',6);
            $table->string('sender_name')->nullable();
            $table->bigInteger('sender_account_no');
            $table->string('reference');
            $table->enum('transaction_type',['transfer','payment'])->default('transfer');
            $table->enum('status',[0,1])->default(0)->comment('0->initiate , 1->success');
            $table->timestamps();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('initiated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['initiated_by']); 
        });
        Schema::dropIfExists('transactions');
    }
};
