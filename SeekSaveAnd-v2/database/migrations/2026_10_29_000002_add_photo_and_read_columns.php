<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('photo_path')->nullable();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('items', fn (Blueprint $t) => $t->dropColumn('photo_path'));
        Schema::table('messages', fn (Blueprint $t) => $t->dropColumn('read_at'));
    }
};
