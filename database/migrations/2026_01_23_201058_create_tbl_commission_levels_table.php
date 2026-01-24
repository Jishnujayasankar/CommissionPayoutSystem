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
        Schema::create('tbl_commission_levels', function (Blueprint $table) {
            $table->bigIncrements('pk_bint_level_id');
            $table->integer('int_level')->unique();
            $table->decimal('dec_percentage', 5, 2);
            $table->boolean('bool_active')->default(true);
            $table->timestamp('tim_created_at')->useCurrent();
            $table->timestamp('tim_updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_commission_levels');
    }
};
