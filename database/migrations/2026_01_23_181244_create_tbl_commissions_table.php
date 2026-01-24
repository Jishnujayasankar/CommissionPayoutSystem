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
        Schema::create('tbl_commissions', function (Blueprint $table) {
            $table->bigIncrements('pk_bint_commission_id');
            $table->unsignedBigInteger('fk_bint_sale_id'); // Sale that generated commission
            $table->unsignedBigInteger('fk_bint_user_id'); // User receiving commission
            $table->integer('int_level'); // Level in hierarchy (1-5)
            $table->decimal('dec_percentage', 5, 2); // Commission percentage
            $table->decimal('dec_amount', 10, 2); // Commission amount
            $table->timestamp('tim_created_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('fk_bint_sale_id')
                  ->references('pk_bint_sale_id')
                  ->on('tbl_sales')
                  ->onDelete('cascade');
                  
            $table->foreign('fk_bint_user_id')
                  ->references('pk_bint_user_id')
                  ->on('tbl_users')
                  ->onDelete('cascade');
                  
            $table->index(['fk_bint_sale_id', 'fk_bint_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_commissions');
    }
};
