<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Poster chooses whether they want admin guidance for returns
            $table->boolean('wants_guidance')->default(false)->after('photo_path');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->string('proof_photo_path')->nullable()->after('proof');
            $table->boolean('claimer_wants_guidance')->default(false)->after('proof_photo_path');
        });

        Schema::table('conversations', function (Blueprint $table) {
            // true when poster OR claimer requested admin guidance
            $table->boolean('guided')->default(false)->after('user_id');
            $table->timestamp('meetup_at')->nullable()->after('guided');
            $table->string('meetup_location')->nullable()->after('meetup_at');
            $table->text('meetup_notes')->nullable()->after('meetup_location');
            $table->string('meetup_status')->nullable()->after('meetup_notes'); // proposed | confirmed | completed | cancelled
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('items', fn (Blueprint $t) => $t->dropColumn('wants_guidance'));
        Schema::table('claims', fn (Blueprint $t) => $t->dropColumn(['proof_photo_path', 'claimer_wants_guidance']));
        Schema::table('conversations', fn (Blueprint $t) => $t->dropColumn([
            'guided', 'meetup_at', 'meetup_location', 'meetup_notes', 'meetup_status',
        ]));
        Schema::table('messages', fn (Blueprint $t) => $t->dropColumn('photo_path'));
    }
};
