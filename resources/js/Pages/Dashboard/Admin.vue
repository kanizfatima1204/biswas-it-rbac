<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props  = defineProps({ stats: Object });
const page   = usePage();
const user   = computed(() => page.props.auth.user);

const roleColor = {
  admin:       'badge-purple',
  team_member: 'badge-blue',
  client:      'badge-yellow',
};

const permissionMap = {
  'admin.access':    { label: 'Admin Access',      admin: true, team: false, client: false },
  'users.view':      { label: 'View Users',         admin: true, team: false, client: false },
  'projects.manage': { label: 'Manage Projects',    admin: true, team: false, client: false },
  'tasks.manage':    { label: 'Manage Tasks',       admin: true, team: true,  client: false },
  'tasks.view':      { label: 'View Tasks',         admin: true, team: true,  client: false },
  'projects.view':   { label: 'View Projects',      admin: true, team: true,  client: true  },
  'requests.create': { label: 'Create Requests',    admin: true, team: false, client: true  },
};

const statCards = computed(() => [
  { icon: '👥', label: 'Total Users',     value: props.stats.total_users,     sub: 'All registered users',   change: '+3 this week', dir: 'up' },
  { icon: '📁', label: 'Active Projects', value: props.stats.active_projects,  sub: 'Currently in progress',  change: '+1 this month', dir: 'up' },
  { icon: '✅', label: 'Pending Tasks',   value: props.stats.pending_tasks,    sub: 'Awaiting completion',     change: '-4 from last week', dir: 'down' },
  { icon: '💚', label: 'System Status',   value: props.stats.system_status,    sub: 'All services running',   change: '99.9% uptime', dir: 'up' },
]);
</script>

<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="page-header animate-in">
      <div class="page-eyebrow">Admin Control Center</div>
      <h1 class="page-title">Agency Overview</h1>
      <p class="page-desc">Manage users, projects, tasks, and system permissions across all roles.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-4 animate-in delay-1" style="margin-bottom: 24px;">
      <div class="stat-card" v-for="(card, i) in statCards" :key="i">
        <div class="stat-icon">{{ card.icon }}</div>
        <div class="stat-label">{{ card.label }}</div>
        <div class="stat-value">{{ card.value }}</div>
        <div class="stat-sub">
          <span :class="['stat-change', card.dir]">{{ card.change }}</span>
          &nbsp;{{ card.sub }}
        </div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-main animate-in delay-2">
      <!-- Left Column -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Recent Users -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">👥 Recent Users</span>
            <span class="badge badge-purple">{{ stats.total_users }} total</span>
          </div>
          <div class="panel-body table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in stats.recent_users" :key="u.id">
                  <td style="font-weight: 600; color: var(--text);">{{ u.name }}</td>
                  <td>{{ u.email }}</td>
                  <td>
                    <span :class="['badge', roleColor[u.role] ?? 'badge-blue']">
                      {{ u.role.replace('_', ' ') }}
                    </span>
                  </td>
                  <td>{{ new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Permission Matrix -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">🔐 Permission Matrix</span>
            <span class="badge badge-green">RBAC</span>
          </div>
          <div class="panel-body table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Permission</th>
                  <th style="text-align:center">Admin</th>
                  <th style="text-align:center">Team</th>
                  <th style="text-align:center">Client</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(perm, slug) in permissionMap" :key="slug">
                  <td>
                    <div style="font-weight:600; color: var(--text); font-size: 13px;">{{ perm.label }}</div>
                    <div style="font-size:11px; color: var(--text-3);">{{ slug }}</div>
                  </td>
                  <td style="text-align:center">
                    <span :class="perm.admin ? 'perm-yes' : 'perm-no'">{{ perm.admin ? '✓' : '—' }}</span>
                  </td>
                  <td style="text-align:center">
                    <span :class="perm.team ? 'perm-yes' : 'perm-no'">{{ perm.team ? '✓' : '—' }}</span>
                  </td>
                  <td style="text-align:center">
                    <span :class="perm.client ? 'perm-yes' : 'perm-no'">{{ perm.client ? '✓' : '—' }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Role Breakdown -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">📊 User Role Breakdown</span>
          </div>
          <div class="panel-body">
            <div style="display: flex; flex-direction: column; gap: 14px;">
              <div v-for="rb in stats.users_breakdown" :key="rb.role">
                <div class="progress-label">
                  <span style="text-transform: capitalize; font-weight: 600; color: var(--text);">
                    {{ rb.role.replace('_', ' ') }}
                  </span>
                  <span>{{ rb.count }} user{{ rb.count !== 1 ? 's' : '' }}</span>
                </div>
                <div class="progress-track">
                  <div class="progress-fill" :style="`width: ${Math.round(rb.count / stats.total_users * 100)}%`"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Activity Feed -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">⚡ Recent Activity</span>
          </div>
          <div class="panel-body">
            <div class="activity-list">
              <div class="activity-item" v-for="(item, i) in stats.activity" :key="i">
                <div class="activity-dot"></div>
                <div>
                  <div class="activity-text" v-html="item.text"></div>
                  <div class="activity-time">{{ item.time }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">🚀 Quick Actions</span>
          </div>
          <div class="panel-body">
            <div class="quick-actions">
              <div class="quick-action">
                <div class="qa-icon">👤</div>
                Add New User
              </div>
              <div class="quick-action">
                <div class="qa-icon">📁</div>
                Create Project
              </div>
              <div class="quick-action">
                <div class="qa-icon">🔐</div>
                Manage Roles
              </div>
              <div class="quick-action">
                <div class="qa-icon">📊</div>
                Export Report
              </div>
              <div class="quick-action">
                <div class="qa-icon">⚙️</div>
                System Settings
              </div>
            </div>
          </div>
        </div>

        <!-- Auth Info -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">🛡 Your Session</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="font-size: 13px; color: var(--text-2);">
              <div class="flex items-center gap-8 mb-4">
                <strong style="color: var(--text);">Logged in as:</strong>
                {{ user?.name }}
              </div>
              <div class="flex items-center gap-8 mb-4">
                <strong style="color: var(--text);">Role:</strong>
                <span class="badge badge-purple">admin</span>
              </div>
              <div>
                <strong style="color: var(--text); display: block; margin-bottom: 8px;">Active Permissions:</strong>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                  <span
                    v-for="perm in stats.permissions"
                    :key="perm.slug"
                    class="badge badge-green"
                    style="font-size: 10px;"
                  >{{ perm.slug }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
