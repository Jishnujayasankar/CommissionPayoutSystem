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
        Schema::create('tbl_sales', function (Blueprint $table) {
            $table->bigIncrements('pk_bint_sale_id');
            $table->unsignedBigInteger('fk_bint_user_id'); // User who made the sale
            $table->decimal('dec_amount', 10, 2); // Sale amount
            $table->timestamp('tim_created_at')->useCurrent();
            
            // Foreign key to users table
            $table->foreign('fk_bint_user_id')
                  ->references('pk_bint_user_id')
                  ->on('tbl_users')
                  ->onDelete('cascade');
                  
            $table->index('fk_bint_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sales');
    }
};
