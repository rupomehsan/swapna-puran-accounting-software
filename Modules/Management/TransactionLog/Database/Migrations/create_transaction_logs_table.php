<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/TransactionLog/Database/create_transaction_logs_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 50)->nullable();
            $table->string('transaction_type', 50)->nullable();
            $table->string('related_type', 100)->nullable();
            $table->bigInteger('related_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('direction', ['credit','debit'])->nullable();
            $table->decimal('balance_after', 10, 2)->nullable();
            $table->datetime('transaction_date')->nullable();
            $table->text('description')->nullable();

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
        Schema::dropIfExists('transaction_logs');
    }
};