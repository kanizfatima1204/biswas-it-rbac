<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref, onUnmounted } from 'vue';

const props = defineProps({ stats: Object });
const page  = usePage();
const user  = computed(() => page.props.auth.user);

// Active Tab State (synced with AppLayout navigation buttons)
const activeTab = ref('dashboard');
const setTab = (tab) => {
  activeTab.value = tab;
  window.location.hash = tab;
};

// Toast notification
const toastMsg = ref('');
const showToast = (msg) => {
  toastMsg.value = msg;
  setTimeout(() => { toastMsg.value = ''; }, 4500);
};

// Interactive Kanban Board State
const tasksList = ref([
  { id: 1, title: 'Build REST API for RBAC permissions', project: 'RBAC System', priority: 'high', status: 'in_progress', due: 'Today' },
  { id: 2, title: 'Refactor Glassmorphism CSS design system', project: 'UI Kit', priority: 'medium', status: 'in_progress', due: 'Tomorrow' },
  { id: 3, title: 'Write integration test suite for auth guards', project: 'RBAC System', priority: 'high', status: 'pending', due: 'Sep 23' },
  { id: 4, title: 'Setup Stripe webhook endpoint handler', project: 'E-Commerce Platform', priority: 'medium', status: 'pending', due: 'Sep 24' },
  { id: 5, title: 'Optimize database indexes on permissions table', project: 'RBAC System', priority: 'low', status: 'done', due: 'Completed' },
  { id: 6, title: 'Peer review PR #47 (Navigation updates)', project: 'UI Kit', priority: 'medium', status: 'done', due: 'Completed' },
]);

const todoTasks = computed(() => tasksList.value.filter(t => t.status === 'pending'));
const inProgressTasks = computed(() => tasksList.value.filter(t => t.status === 'in_progress'));
const doneTasks = computed(() => tasksList.value.filter(t => t.status === 'done'));

// Move Task on Kanban
const moveTask = (task, newStatus) => {
  task.status = newStatus;
  const statusLabels = { pending: 'To Do', in_progress: 'In Progress', done: 'Completed' };
  showToast(`📋 Moved "${task.title}" to ${statusLabels[newStatus]}`);
};

// Modal: Add New Task
const showAddTaskModal = ref(false);
const newTask = ref({
  title: '',
  project: 'RBAC System',
  priority: 'medium',
  status: 'pending',
  due: 'Tomorrow',
});

const addTask = () => {
  if (!newTask.value.title.trim()) {
    alert('Please enter a task title.');
    return;
  }
  tasksList.value.push({
    id: Date.now(),
    title: newTask.value.title,
    project: newTask.value.project,
    priority: newTask.value.priority,
    status: newTask.value.status,
    due: newTask.value.due,
  });
  showToast(`✅ Added task: "${newTask.value.title}"`);
  showAddTaskModal.value = false;
  newTask.value.title = '';
};

// Working Focus / Sprint Work Timer (Pomodoro)
const timerMinutes = ref(25);
const timerSeconds = ref(0);
const isTimerRunning = ref(false);
let timerInterval = null;

const toggleTimer = () => {
  if (isTimerRunning.value) {
    clearInterval(timerInterval);
    isTimerRunning.value = false;
  } else {
    isTimerRunning.value = true;
    timerInterval = setInterval(() => {
      if (timerSeconds.value > 0) {
        timerSeconds.value--;
      } else if (timerMinutes.value > 0) {
        timerMinutes.value--;
        timerSeconds.value = 59;
      } else {
        clearInterval(timerInterval);
        isTimerRunning.value = false;
        showToast('⏰ Sprint Focus Session completed! Take a 5-minute break.');
      }
    }, 1000);
  }
};

const resetTimer = () => {
  clearInterval(timerInterval);
  isTimerRunning.value = false;
  timerMinutes.value = 25;
  timerSeconds.value = 0;
};

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});

// Priority & Status Styling
const priorityClass = {
  high:   'badge-red',
  medium: 'badge-yellow',
  low:    'badge-blue',
};

const statCards = computed(() => [
  { icon: '📋', label: 'Active Sprint Tasks', value: tasksList.value.filter(t => t.status !== 'done').length, sub: 'Assigned in Sprint 34', change: '4 remaining', dir: 'up' },
  { icon: '🏆', label: 'Sprint Done', value: doneTasks.value.length, sub: 'Passed code review', change: '+2 today', dir: 'up' },
  { icon: '⏱️', label: 'Velocity / Pacing', value: '4.8 pt/d', sub: 'Ahead of schedule', change: '+12% speed', dir: 'up' },
  { icon: '🌿', label: 'Active Git Branch', value: 'feat/rbac', sub: '3 commits pending', change: 'Clean tree', dir: 'up' },
]);
</script>

<template>
  <AppLayout :activeTab="activeTab" @tabChange="setTab">

    <!-- Toast Notification -->
    <div v-if="toastMsg" class="toast-notice">
      <span>{{ toastMsg }}</span>
    </div>

    <!-- Modal: Add New Task -->
    <div v-if="showAddTaskModal" class="modal-overlay" @click.self="showAddTaskModal = false">
      <div class="modal-dialog">
        <div class="modal-header">
          <div class="modal-title">
            <span>📋</span>
            Add Sprint Task
          </div>
          <button class="modal-close" @click="showAddTaskModal = false">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Task Title *</label>
            <input v-model="newTask.title" type="text" class="form-input" placeholder="e.g. Implement refresh token rotation" required />
          </div>
          <div class="grid grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Project</label>
              <select v-model="newTask.project" class="form-input" style="background: #1e293b; color: #fff;">
                <option value="RBAC System">RBAC System</option>
                <option value="UI Kit">UI Kit</option>
                <option value="E-Commerce Platform">E-Commerce Platform</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Priority</label>
              <select v-model="newTask.priority" class="form-input" style="background: #1e293b; color: #fff;">
                <option value="high">High Priority</option>
                <option value="medium">Medium Priority</option>
                <option value="low">Low Priority</option>
              </select>
            </div>
          </div>
          <div class="grid grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Initial Column</label>
              <select v-model="newTask.status" class="form-input" style="background: #1e293b; color: #fff;">
                <option value="pending">To Do (Backlog)</option>
                <option value="in_progress">In Progress</option>
                <option value="done">Completed</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Estimated Due</label>
              <input v-model="newTask.due" type="text" class="form-input" placeholder="e.g. Sep 25" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-ghost" @click="showAddTaskModal = false">Cancel</button>
          <button class="btn" style="background: var(--team-grad); color: #fff; font-weight: 600;" @click="addTask">
            ✓ Add to Sprint Board
          </button>
        </div>
      </div>
    </div>

    <!-- Sprint Hero & Focus Timer Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; background: rgba(8,145,178,0.06); border: 1px solid rgba(8,145,178,0.2); border-radius: var(--radius-sm); padding: 14px 20px; margin-bottom: 24px;">
      <div class="flex items-center gap-12">
        <span style="font-size: 24px;">⚡</span>
        <div>
          <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--team-2);">Active Sprint 34</div>
          <div style="font-size: 15px; font-weight: 700; color: #fff;">Biswas IT RBAC & Core Architecture</div>
        </div>
      </div>

      <!-- Working Focus Timer -->
      <div class="flex items-center gap-12" style="background: rgba(0,0,0,0.3); padding: 6px 14px; border-radius: 99px; border: 1px solid var(--border);">
        <span style="font-size: 12px; color: var(--text-3);">Focus Sprint:</span>
        <span style="font-family: monospace; font-size: 16px; font-weight: 700; color: var(--team-2);">
          {{ String(timerMinutes).padStart(2, '0') }}:{{ String(timerSeconds).padStart(2, '0') }}
        </span>
        <button
          class="btn btn-sm"
          :style="isTimerRunning ? 'background: #ef4444; color: #fff;' : 'background: var(--team-grad); color: #fff;'"
          style="padding: 3px 10px; font-size: 11px;"
          @click="toggleTimer"
        >
          {{ isTimerRunning ? '⏸ Pause' : '▶ Start' }}
        </button>
        <button class="btn btn-ghost btn-sm" style="padding: 3px 8px; font-size: 11px;" @click="resetTimer">
          ↺
        </button>
      </div>
    </div>

    <!-- Header -->
    <div class="page-header animate-in">
      <div class="flex items-center justify-between flex-wrap gap-12">
        <div>
          <div class="page-eyebrow" style="background: var(--team-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
            Engineering & Design Studio · Biswas IT Firm
          </div>
          <h1 class="page-title">Developer Workplace</h1>
          <p class="page-desc">Interactive Kanban sprint board, code review queue, assigned tasks, and engineering project status.</p>
        </div>
        <div>
          <button class="btn" style="background: var(--team-grad); color: #fff; font-weight: 700;" @click="showAddTaskModal = true">
            + New Sprint Task
          </button>
        </div>
      </div>
    </div>

    <!-- Sub Navigation Tabs (Allows clicking directly or from sidebar) -->
    <div class="tab-bar animate-in">
      <button :class="['tab-btn', { active: activeTab === 'dashboard' }]" @click="setTab('dashboard')">
        <span>⊞</span> Agile Kanban Board
      </button>
      <button :class="['tab-btn', { active: activeTab === 'tasks' }]" @click="setTab('tasks')">
        <span>📋</span> Detailed Tasks ({{ tasksList.length }})
      </button>
      <button :class="['tab-btn', { active: activeTab === 'projects' }]" @click="setTab('projects')">
        <span>📁</span> Engineering Projects
      </button>
      <button :class="['tab-btn', { active: activeTab === 'calendar' }]" @click="setTab('calendar')">
        <span>📅</span> Sprint Calendar
      </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: KANBAN WORKSPACE (OVERVIEW)                             -->
    <!-- ============================================================== -->
    <div v-if="activeTab === 'dashboard'">
      <!-- Stat Cards -->
      <div class="grid grid-4 animate-in delay-1" style="margin-bottom: 24px;">
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

      <!-- Interactive 3-Column Kanban Board -->
      <div class="kanban-board animate-in delay-2">

        <!-- Column 1: To Do -->
        <div class="kanban-col">
          <div class="kanban-header">
            <div class="kanban-title">
              <span style="color: var(--yellow);">●</span> To Do (Backlog)
            </div>
            <span class="kanban-count">{{ todoTasks.length }}</span>
          </div>

          <div v-for="task in todoTasks" :key="task.id" class="kanban-card">
            <div class="flex items-center justify-between mb-4">
              <span class="badge" style="background: rgba(255,255,255,0.06); font-size: 10px; color: var(--text-2);">
                {{ task.project }}
              </span>
              <span :class="['badge', priorityClass[task.priority]]" style="font-size: 10px;">
                {{ task.priority }}
              </span>
            </div>
            <div class="kanban-card-title">{{ task.title }}</div>
            <div class="kanban-card-meta">
              <span>📅 {{ task.due }}</span>
              <button
                class="btn btn-ghost btn-sm"
                style="color: var(--team-2); font-size: 11px; padding: 2px 8px;"
                @click="moveTask(task, 'in_progress')"
              >
                Start →
              </button>
            </div>
          </div>
          <div v-if="todoTasks.length === 0" style="text-align: center; color: var(--text-3); font-size: 13px; padding: 30px 0;">
            No pending tasks. Great job!
          </div>
        </div>

        <!-- Column 2: In Progress -->
        <div class="kanban-col" style="border-top: 3px solid var(--team-1);">
          <div class="kanban-header">
            <div class="kanban-title">
              <span style="color: var(--team-2);">●</span> In Progress
            </div>
            <span class="kanban-count">{{ inProgressTasks.length }}</span>
          </div>

          <div v-for="task in inProgressTasks" :key="task.id" class="kanban-card">
            <div class="flex items-center justify-between mb-4">
              <span class="badge" style="background: rgba(255,255,255,0.06); font-size: 10px; color: var(--text-2);">
                {{ task.project }}
              </span>
              <span :class="['badge', priorityClass[task.priority]]" style="font-size: 10px;">
                {{ task.priority }}
              </span>
            </div>
            <div class="kanban-card-title">{{ task.title }}</div>
            <div class="kanban-card-meta">
              <button
                class="btn btn-ghost btn-sm"
                style="color: var(--text-3); font-size: 11px; padding: 2px 6px;"
                @click="moveTask(task, 'pending')"
              >
                ← Back
              </button>
              <button
                class="btn btn-ghost btn-sm"
                style="color: var(--green); font-size: 11px; padding: 2px 6px; font-weight: 700;"
                @click="moveTask(task, 'done')"
              >
                ✓ Complete
              </button>
            </div>
          </div>
        </div>

        <!-- Column 3: Completed -->
        <div class="kanban-col" style="border-top: 3px solid var(--green);">
          <div class="kanban-header">
            <div class="kanban-title">
              <span style="color: var(--green);">●</span> Done & Verified
            </div>
            <span class="kanban-count">{{ doneTasks.length }}</span>
          </div>

          <div v-for="task in doneTasks" :key="task.id" class="kanban-card" style="opacity: 0.85;">
            <div class="flex items-center justify-between mb-4">
              <span class="badge" style="background: rgba(255,255,255,0.06); font-size: 10px; color: var(--text-2);">
                {{ task.project }}
              </span>
              <span class="badge badge-green" style="font-size: 10px;">
                ✓ verified
              </span>
            </div>
            <div class="kanban-card-title" style="text-decoration: line-through; color: var(--text-2);">
              {{ task.title }}
            </div>
            <div class="kanban-card-meta">
              <span>🎉 Sprint ready</span>
              <button
                class="btn btn-ghost btn-sm"
                style="color: var(--text-3); font-size: 10px; padding: 2px 6px;"
                @click="moveTask(task, 'in_progress')"
              >
                Reopen ↺
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 2: DETAILED TASKS LIST                                     -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'tasks'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">📋 Sprint 34 Backlog & Tasks</span>
          <button class="btn btn-sm" style="background: var(--team-grad); color: #fff;" @click="showAddTaskModal = true">+ Add Task</button>
        </div>
        <div class="panel-body table-wrap">
          <table>
            <thead>
              <tr>
                <th>Task Title</th>
                <th>Project</th>
                <th>Priority</th>
                <th>Current Status</th>
                <th>Due Date</th>
                <th style="text-align: right;">Move Stage</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in tasksList" :key="t.id">
                <td style="font-weight: 600; color: var(--text);">{{ t.title }}</td>
                <td style="color: var(--text-2);">{{ t.project }}</td>
                <td><span :class="['badge', priorityClass[t.priority]]">{{ t.priority }}</span></td>
                <td>
                  <span :class="['badge', t.status === 'done' ? 'badge-green' : t.status === 'in_progress' ? 'badge-blue' : 'badge-yellow']">
                    {{ t.status === 'done' ? '✓ Completed' : t.status === 'in_progress' ? '⚙ In Progress' : '⏳ Backlog' }}
                  </span>
                </td>
                <td style="color: var(--text-3); font-size: 12px;">{{ t.due }}</td>
                <td style="text-align: right;">
                  <button
                    v-if="t.status !== 'done'"
                    class="btn btn-ghost btn-sm"
                    style="color: var(--green); font-size: 11px;"
                    @click="moveTask(t, 'done')"
                  >
                    ✓ Mark Done
                  </button>
                  <button
                    v-else
                    class="btn btn-ghost btn-sm"
                    style="color: var(--team-2); font-size: 11px;"
                    @click="moveTask(t, 'in_progress')"
                  >
                    ↺ Reopen
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 3: ENGINEERING PROJECTS                                    -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'projects'" class="animate-in">
      <div class="grid grid-2" style="gap: 20px;">
        <div v-for="proj in stats.projects" :key="proj.name" class="panel">
          <div class="panel-header">
            <div>
              <div class="panel-title">{{ proj.name }}</div>
              <div style="font-size: 12px; color: var(--text-3); margin-top: 2px;">{{ proj.tasks_done }}/{{ proj.tasks_total }} tasks delivered</div>
            </div>
            <span class="badge badge-blue">{{ proj.progress }}%</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 14px;">
            <div class="progress-track">
              <div class="progress-fill" :style="`width: ${proj.progress}%; background: var(--team-grad)`"></div>
            </div>
            <div style="font-size: 13px; color: var(--text-2);">
              Target milestone release active. Next PR review cycle scheduled today.
            </div>
            <div class="flex items-center justify-between mt-8 text-xs text-muted">
              <span>Branch: <code>main</code></span>
              <button class="btn btn-ghost btn-sm" @click="showToast(`📁 Opening repository logs for ${proj.name}`)">Git History ↗</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 4: SPRINT CALENDAR                                         -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'calendar'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">📅 Sprint 34 Timeline & Releases</span>
        </div>
        <div class="panel-body">
          <div class="activity-list">
            <div class="activity-item">
              <div class="activity-dot" style="background: var(--green);"></div>
              <div>
                <div class="activity-text"><strong>Sprint Planning & Architecture Alignment</strong></div>
                <div class="activity-time">Completed · Sep 18, 2026</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot" style="background: var(--team-1);"></div>
              <div>
                <div class="activity-text"><strong>Mid-Sprint Code Freeze & QA Run</strong></div>
                <div class="activity-time">Upcoming · Sep 23, 2026 (10:00 AM)</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot" style="background: var(--yellow);"></div>
              <div>
                <div class="activity-text"><strong>Client Staging Demo Presentation</strong></div>
                <div class="activity-time">Scheduled · Sep 25, 2026 (03:00 PM)</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>
