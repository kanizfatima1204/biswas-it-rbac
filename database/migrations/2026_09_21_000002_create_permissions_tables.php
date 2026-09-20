<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); $table->string('slug')->unique(); $table->string('name'); $table->string('description')->nullable();
        });
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->string('role'); $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role','permission_id']); $table->index('role');
        });
    }
    public function down(): void { Schema::dropIfExists('role_permissions'); Schema::dropIfExists('permissions'); }
};
