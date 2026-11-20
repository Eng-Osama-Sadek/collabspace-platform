<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Conversation;
use App\Models\DailyUpdate;
use App\Models\Meeting;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $admin = User::updateOrCreate(
            ['email' => 'admin@collabspace.local'],
            [
                'name' => 'Osama Sadek',
                'password' => $password,
                'phone' => '01000000001',
                'job_title' => 'Product & Systems Lead',
                'department' => 'Back End',
                'role' => 'admin',
                'years_of_experience' => 7,
                'availability_status' => 'available',
                'about' => 'Leading workspace architecture, delivery milestones, and cross-functional coordination.',
                'hours_worked' => 142,
                'joined_at' => now()->subMonths(8)->toDateString(),
            ]
        );

        $uiUser = User::updateOrCreate(
            ['email' => 'uiux@collabspace.local'],
            [
                'name' => 'Salma Hassan',
                'password' => $password,
                'phone' => '01000000002',
                'job_title' => 'Senior UI/UX Designer',
                'department' => 'UI/UX',
                'role' => 'member',
                'years_of_experience' => 4,
                'availability_status' => 'available',
                'about' => 'Designing clean task boards, user flows, and responsive collaboration interfaces.',
                'hours_worked' => 118,
                'joined_at' => now()->subMonths(6)->toDateString(),
            ]
        );

        $feUser = User::updateOrCreate(
            ['email' => 'frontend@collabspace.local'],
            [
                'name' => 'Karim Adel',
                'password' => $password,
                'phone' => '01000000003',
                'job_title' => 'Frontend Engineer',
                'department' => 'Front End',
                'role' => 'member',
                'years_of_experience' => 3,
                'availability_status' => 'available',
                'about' => 'Building interactive filters, modals, and accessible layouts across web views.',
                'hours_worked' => 104,
                'joined_at' => now()->subMonths(5)->toDateString(),
            ]
        );

        $beUser = User::updateOrCreate(
            ['email' => 'backend@collabspace.local'],
            [
                'name' => 'Nourhan Tarek',
                'password' => $password,
                'phone' => '01000000004',
                'job_title' => 'Laravel Backend Engineer',
                'department' => 'Back End',
                'role' => 'member',
                'years_of_experience' => 5,
                'availability_status' => 'available',
                'about' => 'Architecting relational schemas, controllers, file workflows, and reporting endpoints.',
                'hours_worked' => 129,
                'joined_at' => now()->subMonths(7)->toDateString(),
            ]
        );

        $teamIds = [$admin->id, $uiUser->id, $feUser->id, $beUser->id];

        $alpha = Project::updateOrCreate(
            ['name' => 'Alpha'],
            [
                'description' => 'Core workspace and task management platform.',
                'project_type' => 'saas',
                'priority' => 'high',
                'status' => 'active',
                'wiki_notes' => "## Alpha Overview\n- Focus on task clarity, daily updates, and sprint visibility.\n- Keep weekly release notes and meeting decisions documented here.",
                'start_date' => now()->subDays(14)->toDateString(),
                'end_date' => now()->addDays(20)->toDateString(),
                'created_by' => $admin->id,
            ]
        );
        $alpha->members()->sync($teamIds);

        $sepetGo = Project::updateOrCreate(
            ['name' => 'SepetGo'],
            [
                'description' => 'E-commerce and delivery operations module.',
                'project_type' => 'operations',
                'priority' => 'medium',
                'status' => 'active',
                'wiki_notes' => "## SepetGo Notes\n- Track order dispatch workflows, attachments, and daily status blockers.",
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'created_by' => $admin->id,
            ]
        );
        $sepetGo->members()->sync($teamIds);

        $tasksData = [
            [
                'project_id' => $alpha->id,
                'title' => 'Design Kanban Board & Dashboard Wireframes',
                'description' => 'Prepare layout states for To-do, In Progress, In Review, and Completed.',
                'status' => 'completed',
                'priority' => 'high',
                'track' => 'UI/UX',
                'progress' => 100,
                'start_date' => now()->subDays(10)->toDateString(),
                'due_date' => now()->subDays(2)->toDateString(),
                'created_by' => $admin->id,
                'assignees' => [$uiUser->id],
            ],
            [
                'project_id' => $alpha->id,
                'title' => 'Build Responsive Task Filters & Modals',
                'description' => 'Implement task filtering by project, track, and priority.',
                'status' => 'in_review',
                'priority' => 'medium',
                'track' => 'Front End',
                'progress' => 80,
                'start_date' => now()->subDays(6)->toDateString(),
                'due_date' => now()->addDays(2)->toDateString(),
                'created_by' => $admin->id,
                'assignees' => [$feUser->id],
            ],
            [
                'project_id' => $alpha->id,
                'title' => 'Implement Projects, Tasks & Reports Controllers',
                'description' => 'Add validation, database queries, and status transitions in Laravel.',
                'status' => 'in_progress',
                'priority' => 'high',
                'track' => 'Back End',
                'progress' => 60,
                'start_date' => now()->subDays(4)->toDateString(),
                'due_date' => now()->addDays(4)->toDateString(),
                'created_by' => $admin->id,
                'assignees' => [$admin->id, $beUser->id],
            ],
            [
                'project_id' => $sepetGo->id,
                'title' => 'Set Up File Storage & Report Submission Flow',
                'description' => 'Support file uploads and detailed report submissions per project.',
                'status' => 'todo',
                'priority' => 'low',
                'track' => 'Back End',
                'progress' => 0,
                'start_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'created_by' => $admin->id,
                'assignees' => [$beUser->id],
            ],
        ];

        $firstTask = null;
        foreach ($tasksData as $item) {
            $assignees = $item['assignees'];
            unset($item['assignees']);

            $task = Task::updateOrCreate(
                ['title' => $item['title'], 'project_id' => $item['project_id']],
                $item
            );
            $task->assignees()->sync($assignees);

            if (! $firstTask) {
                $firstTask = $task;
            }
        }

        ProjectFile::updateOrCreate(
            ['name' => 'project-scope-alpha.pdf', 'project_id' => $alpha->id],
            [
                'task_id' => $firstTask?->id,
                'uploaded_by' => $admin->id,
                'file_path' => 'projects/alpha/project-scope-alpha.pdf',
                'mime_type' => 'application/pdf',
                'size_bytes' => 245760,
            ]
        );

        $publicChannel = Conversation::updateOrCreate(
            ['channel_key' => 'public'],
            [
                'type' => 'channel',
                'title' => 'Public Channel',
                'project_id' => $alpha->id,
                'is_pinned' => true,
                'is_archived' => false,
            ]
        );
        $publicChannel->participants()->sync($teamIds);

        Message::firstOrCreate(
            [
                'conversation_id' => $publicChannel->id,
                'sender_id' => $admin->id,
                'body' => 'Welcome to Collabspace! Please update your task progress today.',
            ],
            ['attachment_type' => 'none']
        );

        $directChat = Conversation::updateOrCreate(
            ['title' => 'Direct Chat - Nourhan Tarek', 'type' => 'direct'],
            [
                'project_id' => $alpha->id,
                'is_pinned' => false,
                'is_archived' => false,
            ]
        );
        $directChat->participants()->sync([$admin->id, $beUser->id]);

        Message::firstOrCreate(
            [
                'conversation_id' => $directChat->id,
                'sender_id' => $beUser->id,
                'body' => 'Backend endpoints and database seeders are ready for review.',
            ],
            ['attachment_type' => 'none']
        );

        $meeting = Meeting::updateOrCreate(
            ['title' => 'Daily Sync & Sprint Review'],
            [
                'project_id' => $alpha->id,
                'agenda' => 'Review task board progress, blockers, and upcoming deliverables.',
                'meeting_link' => 'https://meet.google.com/collabspace-sync',
                'transcript' => "[00:01] Osama Sadek: Let's review Alpha sprint progress and blockers.\n[02:01] Salma Hassan: Kanban wireframes and task states are completed.\n[04:48] Karim Adel: Task filters and modals are ready for review.\n[06:15] Nourhan Tarek: Database seeders and controllers are verified.",
                'ai_summary' => 'The team reviewed Alpha progress across UI/UX, Front End, and Back End, confirming wireframes are complete and filters/controllers are moving forward smoothly.',
                'key_points' => "• Wireframes for To-do, In Progress, In Review, and Completed are done.\n• Task filters and modals are in review.\n• Seeders and controllers are in place for full testing.",
                'start_at' => now()->addHours(2),
                'end_at' => now()->addHours(3),
                'created_by' => $admin->id,
            ]
        );
        $meeting->attendees()->sync($teamIds);

        Report::updateOrCreate(
            ['report_type' => 'Project Progress', 'project_id' => $alpha->id],
            [
                'submitted_by' => $admin->id,
                'note' => 'Alpha workspace is on track with active UI, frontend, and backend tasks.',
                'start_date' => now()->subDays(7)->toDateString(),
                'end_date' => now()->toDateString(),
            ]
        );

        DailyUpdate::updateOrCreate(
            [
                'user_id' => $beUser->id,
                'project_id' => $alpha->id,
                'update_date' => now()->toDateString(),
            ],
            [
                'completed_today' => 'Verified migrations, models, and database seeders for Alpha and SepetGo.',
                'planned_next' => 'Finalize report export checks and daily update views.',
                'blockers' => null,
            ]
        );

        ActivityLog::firstOrCreate(
            [
                'user_id' => $admin->id,
                'project_id' => $alpha->id,
                'action_text' => 'Scheduled Daily Sync & Sprint Review for Alpha project',
            ]
        );

        ActivityLog::firstOrCreate(
            [
                'user_id' => $uiUser->id,
                'project_id' => $alpha->id,
                'action_text' => 'Completed Design Kanban Board & Dashboard Wireframes',
            ]
        );

        Notification::firstOrCreate(
            [
                'user_id' => $admin->id,
                'title' => 'Meeting Reminder: Daily Sync & Sprint Review',
            ],
            [
                'type' => 'meeting_reminder',
                'message' => 'Your scheduled team meeting starts soon.',
                'is_read' => false,
            ]
        );
    }
}