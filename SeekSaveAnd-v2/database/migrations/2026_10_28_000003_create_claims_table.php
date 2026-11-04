<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('proof');
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->timestamps();

            $table->unique(['item_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
