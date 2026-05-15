<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_adjustments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->enum('adjustment_type', ['increase', 'decrease'])->nullable();
            $table->integer('from_shares')->nullable();
            $table->integer('to_shares')->nullable();
            $table->integer('shares_delta')->nullable();
            $table->integer('months_elapsed')->default(0);
            $table->decimal('share_price', 10, 2)->default(0);
            $table->decimal('expected_old', 12, 2)->default(0);
            $table->decimal('expected_new', 12, 2)->default(0);
            $table->decimal('paid_so_far', 12, 2)->default(0);
            $table->decimal('adjustment_amount', 12, 2)->default(0);
            $table->enum('refund_destination', ['withdrawal', 'extra_savings'])->nullable();
            $table->bigInteger('linked_deposit_id')->nullable();
            $table->bigInteger('linked_withdrawal_id')->nullable();
            $table->text('note')->nullable();
            $table->date('effective_date')->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_adjustments');
    }
};
