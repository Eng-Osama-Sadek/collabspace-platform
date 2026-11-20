<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('about')->nullable()->after('job_title');
            $table->unsignedInteger('hours_worked')->default(0)->after('years_of_experience');
            $table->date('joined_at')->nullable()->after('hours_worked');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type')->default('saas')->after('description');
            $table->text('wiki_notes')->nullable()->after('status');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->longText('transcript')->nullable()->after('agenda');
            $table->text('ai_summary')->nullable()->after('transcript');
            $table->text('key_points')->nullable()->after('ai_summary');
        });

        Schema::create('daily_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('update_date');
            $table->text('completed_today');
            $table->text('planned_next');
            $table->text('blockers')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'project_id', 'update_date']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('action_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('daily_updates');

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['transcript', 'ai_summary', 'key_points']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['project_type', 'wiki_notes']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['about', 'hours_worked', 'joined_at']);
        });
    }
};