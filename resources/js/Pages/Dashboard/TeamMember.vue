<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ stats: Object });
const page  = usePage();
const user  = computed(() => page.props.auth.user);

const priorityClass = {
  high:   'badge-red',
  medium: 'badge-yellow',
  low:    'badge-blue',
};
const statusClass = {
  in_progress: 'badge-blue',
  pending:     'badge-yellow',
  done:        'badge-green',
};
const statusLabel = {
  in_progress: 'In Progress',
  pending:     'Pending',
  done:        'Done',
};

const statCards = computed(() => [
  { icon: '✅', label: 'Assigned Tasks',     value: props.stats.assigned_tasks,      sub: 'Currently assigned to you', change: '+2 since Monday', dir: 'up' },
  { icon: '🏆', label: 'Done This Week',     value: props.stats.completed_this_week, sub: 'Completed tasks',            change: 'Great pace!',       dir: 'up' },
  { icon: '📁', label: 'Open Projects',      value: props.stats.open_projects,       sub: 'Active involvement',         change: '2 near deadline',   dir: 'down' },
  { icon: '⚖️', label: 'Workload',           value: props.stats.workload,            sub: 'Current load status',        change: 'On track',          dir: 'up' },
]);
</script>

<template>
  <AppLayout>
    <!-- Header -->
    <div class="page-header animate-in">
      <div class="page-eyebrow" style="--accent-grad: var(--team-grad); background: var(--team-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
        Team Workspace
      </div>
      <h1 class="page-title">My Work Hub</h1>
      <p class="page-desc">Track assigned tasks, manage project progress, and collaborate with the team.</p>
    </div>

    <!-- Stat Cards (teal accent) -->
    <div class="grid grid-4 animate-in delay-1" style="margin-bottom: 24px; --accent-1: var(--team-1); --accent-2: var(--team-2); --accent-grad: var(--team-grad);">
      <div class="stat-card" v-for="(card, i) in statCards" :key="i">
        <div class="stat-icon" style="background: var(--team-grad); box-shadow: 0 4px 12px rgba(8,145,178,0.35);">
          {{ card.icon }}
        </div>
        <div class="stat-label">{{ card.label }}</div>
        <div class="stat-value">{{ card.value }}</div>
        <div class="stat-sub">
          <span :class="['stat-change', card.dir]">{{ card.change }}</span>
          &nbsp;· {{ card.sub }}
        </div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-main animate-in delay-2">
      <!-- Tasks -->
      <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">📋 My Tasks</span>
            <span class="badge badge-blue">{{ stats.assigned_tasks }} active</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 10px;">
            <div class="task-card" v-for="(task, i) in stats.tasks" :key="i">
              <div class="flex items-center gap-8 mb-4">
                <span :class="['badge', statusClass[task.status]]" style="font-size: 10px;">
                  {{ statusLabel[task.status] }}
                </span>
                <span :class="['badge', priorityClass[task.priority]]" style="font-size: 10px;">
                  {{ task.priority }} priority
                </span>
              </div>
              <div class="task-title">{{ task.title }}</div>
              <div class="task-meta">
                <span>📁 {{ task.project }}</span>
                <span>•</span>
                <span>📅 {{ task.due }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Project Progress -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">📁 Project Progress</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 18px;">
            <div v-for="proj in stats.projects" :key="proj.name">
              <div class="progress-label">
                <span style="font-weight: 600; color: var(--text);">{{ proj.name }}</span>
                <span>{{ proj.progress }}%</span>
              </div>
              <div class="progress-track">
                <div
                  class="progress-fill"
                  :style="`width: ${proj.progress}%; background: var(--team-grad)`"
                ></div>
              </div>
              <div style="font-size: 11px; color: var(--text-3); margin-top: 4px;">
                {{ proj.tasks_done }}/{{ proj.tasks_total }} tasks completed
              </div>
            </div>
          </div>
        </div>

        <!-- Access Info -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">🔒 Your Access Level</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--text-2);">
            <div>✅ <strong style="color:var(--text)">tasks.view</strong> — View assigned tasks</div>
            <div>✅ <strong style="color:var(--text)">tasks.manage</strong> — Update task status</div>
            <div>✅ <strong style="color:var(--text)">projects.view</strong> — View project details</div>
            <div style="opacity: 0.4;">✗ <span style="text-decoration: line-through">admin.access</span> — Admin only</div>
            <div style="opacity: 0.4;">✗ <span style="text-decoration: line-through">users.view</span> — Admin only</div>
            <div style="margin-top: 12px; padding: 10px; background: rgba(8,145,178,0.08); border: 1px solid rgba(8,145,178,0.2); border-radius: 8px; color: var(--team-2); font-size: 12px;">
              🔐 Route /team-only requires <strong>role:admin,team_member</strong>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">⚡ Quick Actions</span>
          </div>
          <div class="panel-body">
            <div class="quick-actions">
              <div class="quick-action">
                <div class="qa-icon">✅</div>
                Mark Task Done
              </div>
              <div class="quick-action">
                <div class="qa-icon">💬</div>
                Add Comment
              </div>
              <div class="quick-action">
                <div class="qa-icon">📅</div>
                View Calendar
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
