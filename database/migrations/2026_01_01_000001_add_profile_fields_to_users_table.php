<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('job_title')->default('Team Member')->after('avatar');
            $table->string('department')->default('Back End')->after('job_title'); // UI/UX, Front End, Back End, Graphic Designers
            $table->string('role')->default('member')->after('department'); // admin, manager, member
            $table->unsignedTinyInteger('years_of_experience')->default(1)->after('role');
            $table->string('availability_status')->default('available')->after('years_of_experience'); // available, busy, away
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'avatar',
                'job_title',
                'department',
                'role',
                'years_of_experience',
                'availability_status',
            ]);
        });
    }
};