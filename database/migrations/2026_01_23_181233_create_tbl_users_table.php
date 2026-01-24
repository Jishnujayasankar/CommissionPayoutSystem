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
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->bigIncrements('pk_bint_user_id');
            $table->string('vchr_name', 100);
            $table->string('vchr_email', 100)->unique();
            $table->unsignedBigInteger('fk_bint_parent_id')->nullable(); // Self-referencing for hierarchy
            $table->timestamp('tim_created_at')->useCurrent();
            $table->timestamp('tim_updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign key constraint for parent-child relationship
            $table->foreign('fk_bint_parent_id')
                  ->references('pk_bint_user_id')
                  ->on('tbl_users')
                  ->onDelete('cascade');
                  
            $table->index('fk_bint_parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_users');
    }
};
