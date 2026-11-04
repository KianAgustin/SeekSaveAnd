<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('campus')->nullable()->after('email');
            $table->string('program')->nullable();
            $table->string('year')->nullable();
            $table->string('section')->nullable();
            $table->string('student_number')->nullable()->unique();
            $table->string('contact')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['campus', 'program', 'year', 'section', 'student_number', 'contact']);
        });
    }
};
