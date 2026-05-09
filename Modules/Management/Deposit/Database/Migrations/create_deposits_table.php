<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/Deposit/Database/create_deposits_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('voucher_no', 50)->nullable();
            $table->enum('deposit_type', ['share_deposit','extra_savings'])->nullable();
            $table->string('amount', 100)->nullable();
            $table->date('for_month')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->bigInteger('due_id')->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('received_by')->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};