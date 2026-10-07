<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            // Drop FK + index before changing nullability
            $table->dropForeign(['material_id']);

            $table->unsignedBigInteger('material_id')->nullable()->change();
            $table->string('material_name_manual')->nullable()->after('material_id');

            // Re-add FK with nullable allowed
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn('material_name_manual');
            $table->unsignedBigInteger('material_id')->nullable(false)->change();
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }
};
