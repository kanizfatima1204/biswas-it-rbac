<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $role = $user->role;

        $component = match ($role) {
            'admin'       => 'Dashboard/Admin',
            'team_member' => 'Dashboard/TeamMember',
            default       => 'Dashboard/Client',
        };

        $stats = match ($role) {
            'admin' => [
                'total_users'      => User::count(),
                'active_projects'  => 8,
                'pending_tasks'    => 17,
                'system_status'    => 'Healthy',
                'users_breakdown'  => [
                    ['role' => 'admin',       'count' => User::where('role', 'admin')->count()],
                    ['role' => 'team_member', 'count' => User::where('role', 'team_member')->count()],
                    ['role' => 'client',      'count' => User::where('role', 'client')->count()],
                ],
                'recent_users' => User::latest()->limit(5)->get(['id','name','email','role','created_at']),
                'permissions'  => $user->permissions()->get(['slug','name','description']),
                'activity' => [
                    ['text' => '<strong>New client</strong> registered — Client User', 'time' => '2 min ago'],
                    ['text' => '<strong>Project Alpha</strong> moved to In Progress',  'time' => '18 min ago'],
                    ['text' => '<strong>Task #12</strong> marked completed by Team',   'time' => '1 hr ago'],
                    ['text' => '<strong>Permission</strong> updated for team role',     'time' => '3 hr ago'],
                    ['text' => '<strong>System health</strong> check passed ✓',        'time' => '6 hr ago'],
                ],
            ],
            'team_member' => [
                'assigned_tasks'      => 6,
                'completed_this_week' => 12,
                'open_projects'       => 4,
                'workload'            => 'Balanced',
                'tasks' => [
                    ['title' => 'Build REST API endpoints',   'project' => 'Project Alpha', 'priority' => 'high',   'status' => 'in_progress', 'due' => 'Today'],
                    ['title' => 'Design system components',   'project' => 'UI Kit',        'priority' => 'medium', 'status' => 'in_progress', 'due' => 'Tomorrow'],
                    ['title' => 'Write unit tests for auth',  'project' => 'RBAC System',   'priority' => 'high',   'status' => 'pending',     'due' => 'Sep 23'],
                    ['title' => 'Deploy to staging server',   'project' => 'Project Beta',  'priority' => 'low',    'status' => 'pending',     'due' => 'Sep 25'],
                    ['title' => 'Code review — PR #47',       'project' => 'Project Alpha', 'priority' => 'medium', 'status' => 'done',        'due' => 'Done'],
                    ['title' => 'Update documentation',       'project' => 'RBAC System',   'priority' => 'low',    'status' => 'done',        'due' => 'Done'],
                ],
                'projects' => [
                    ['name' => 'Project Alpha', 'progress' => 72, 'tasks_done' => 18, 'tasks_total' => 25],
                    ['name' => 'UI Kit',        'progress' => 45, 'tasks_done' => 9,  'tasks_total' => 20],
                    ['name' => 'RBAC System',   'progress' => 88, 'tasks_done' => 22, 'tasks_total' => 25],
                    ['name' => 'Project Beta',  'progress' => 20, 'tasks_done' => 4,  'tasks_total' => 20],
                ],
            ],
            default => [
                'projects'              => 2,
                'open_requests'         => 1,
                'completed_deliverables'=> 7,
                'account_status'        => 'Active',
                'projects_list' => [
                    ['name' => 'E-Commerce Platform', 'status' => 'in_progress', 'progress' => 65, 'deadline' => 'Oct 15, 2026'],
                    ['name' => 'Corporate Website',   'status' => 'review',      'progress' => 90, 'deadline' => 'Sep 28, 2026'],
                ],
                'deliverables' => [
                    ['title' => 'Homepage Design',     'date' => 'Sep 10', 'status' => 'delivered'],
                    ['title' => 'Mobile App Mockups',  'date' => 'Sep 5',  'status' => 'delivered'],
                    ['title' => 'API Documentation',   'date' => 'Aug 28', 'status' => 'delivered'],
                    ['title' => 'Final QA Report',     'date' => 'Pending','status' => 'pending'],
                ],
            ],
        };

        return Inertia::render($component, [
            'stats' => $stats,
        ]);
    }
}
