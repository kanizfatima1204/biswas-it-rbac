<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

// Interactive User Management State
const userSearch = ref('');
const usersList = ref([
  { id: 1, name: 'Admin User', email: 'admin@biswasit.test', role: 'admin', status: 'Active', joined: 'Sep 21, 2026' },
  { id: 2, name: 'Team Member', email: 'team@biswasit.test', role: 'team_member', status: 'Active', joined: 'Sep 21, 2026' },
  { id: 3, name: 'Client User', email: 'client@biswasit.test', role: 'client', status: 'Active', joined: 'Sep 21, 2026' },
  { id: 4, name: 'Rashid Khan', email: 'rashid@biswasit.test', role: 'team_member', status: 'Active', joined: 'Sep 18, 2026' },
  { id: 5, name: 'Apex Tech Corp', email: 'billing@apextech.test', role: 'client', status: 'Active', joined: 'Sep 14, 2026' },
]);

const filteredUsers = computed(() => {
  if (!userSearch.value.trim()) return usersList.value;
  const q = userSearch.value.toLowerCase();
  return usersList.value.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q) || u.role.includes(q));
});

// Modal: Add New User
const showAddUserModal = ref(false);
const newUser = ref({ name: '', email: '', role: 'team_member' });
const addUser = () => {
  if (!newUser.value.name || !newUser.value.email) {
    alert('Please fill in name and email.');
    return;
  }
  usersList.value.unshift({
    id: Date.now(),
    name: newUser.value.name,
    email: newUser.value.email,
    role: newUser.value.role,
    status: 'Active',
    joined: 'Just now',
  });
  showToast(`✅ Created user ${newUser.value.name} with role ${newUser.value.role}`);
  showAddUserModal.value = false;
  newUser.value = { name: '', email: '', role: 'team_member' };
};

const toggleUserRole = (u) => {
  const next = { admin: 'team_member', team_member: 'client', client: 'admin' };
  u.role = next[u.role] ?? 'team_member';
  showToast(`🔄 Role for ${u.name} updated to ${u.role}`);
};

// System Health Simulation
const cacheClearing = ref(false);
const clearCache = () => {
  cacheClearing.value = true;
  setTimeout(() => {
    cacheClearing.value = false;
    showToast('⚡ Application & route cache flushed successfully.');
  }, 600);
};

const maintenanceMode = ref(false);
const toggleMaintenance = () => {
  maintenanceMode.value = !maintenanceMode.value;
  showToast(maintenanceMode.value ? '⚠️ Maintenance mode activated.' : '✅ Maintenance mode deactivated.');
};

// Interactive Permission Matrix
const permSearch = ref('');
const permissions = ref([
  { slug: 'admin.access', name: 'Admin Console Access', admin: true, team: false, client: false, desc: 'Full root access to system settings & RBAC management' },
  { slug: 'users.manage', name: 'User Management', admin: true, team: false, client: false, desc: 'Create, modify, and terminate system user accounts' },
  { slug: 'roles.assign', name: 'Role Assignment', admin: true, team: false, client: false, desc: 'Reassign permissions to organizational roles' },
  { slug: 'projects.manage', name: 'Project Governance', admin: true, team: false, client: false, desc: 'Create projects, set budgets, and assign lead engineers' },
  { slug: 'tasks.assign', name: 'Task Allocation', admin: true, team: true, client: false, desc: 'Dispatch tasks to developers and review completion' },
  { slug: 'tasks.update', name: 'Task Status Updates', admin: true, team: true, client: false, desc: 'Update execution status of assigned backlog items' },
  { slug: 'projects.view', name: 'Project Oversight', admin: true, team: true, client: true, desc: 'Inspect project progress and milestone timelines' },
  { slug: 'client.requests', name: 'Service Request Dispatch', admin: true, team: false, client: true, desc: 'Authorize and submit service requests' },
  { slug: 'reports.view', name: 'Financial & Audit Analytics', admin: true, team: false, client: false, desc: 'Inspect agency revenue and performance metrics' },
]);

const filteredPermissions = computed(() => {
  if (!permSearch.value.trim()) return permissions.value;
  const q = permSearch.value.toLowerCase();
  return permissions.value.filter(p => p.name.toLowerCase().includes(q) || p.slug.toLowerCase().includes(q));
});

const togglePermission = (perm, roleKey) => {
  perm[roleKey] = !perm[roleKey];
  showToast(`⚡ Updated permission ${perm.slug} for ${roleKey} role.`);
};

// Agency Projects
const agencyProjects = ref([
  { name: 'E-Commerce Platform', client: 'Retail Alpha Group', budget: '$18,500', spent: '$12,000', lead: 'Tanvir I.', status: 'On Track', health: 'Healthy' },
  { name: 'Corporate Brand Portal', client: 'Apex Tech Corp', budget: '$9,200', spent: '$8,100', lead: 'Rahim B.', status: 'Review', health: 'Healthy' },
  { name: 'Mobile Banking App', client: 'FinBank Global', budget: '$34,000', spent: '$14,500', lead: 'Farhan K.', status: 'Active', health: 'Healthy' },
  { name: 'AI Customer Assistant', client: 'Biswas Labs Internal', budget: '$6,000', spent: '$5,800', lead: 'Team Dev', status: 'Testing', health: 'Attention' },
]);

const roleBadgeColor = {
  admin: 'badge-purple',
  team_member: 'badge-blue',
  client: 'badge-yellow',
};

const statCards = computed(() => [
  { icon: '👥', label: 'Total Accounts', value: usersList.value.length, sub: 'Active RBAC users', change: '+2 today', dir: 'up' },
  { icon: '📁', label: 'Active Projects', value: agencyProjects.value.length, sub: 'Engaged clients', change: '100% capacity', dir: 'up' },
  { icon: '💰', label: 'Monthly Revenue', value: '$24,850', sub: 'Gross billable', change: '+18.4% vs Aug', dir: 'up' },
  { icon: '⚡', label: 'API Security', value: '100% Pass', sub: 'Rate throttled', change: '5 req/min cap', dir: 'up' },
]);
</script>

<template>
  <AppLayout :activeTab="activeTab" @tabChange="setTab">

    <!-- Toast Notification -->
    <div v-if="toastMsg" class="toast-notice">
      <span>{{ toastMsg }}</span>
    </div>

    <!-- Modal: Add New User -->
    <div v-if="showAddUserModal" class="modal-overlay" @click.self="showAddUserModal = false">
      <div class="modal-dialog">
        <div class="modal-header">
          <div class="modal-title">
            <span>👤</span>
            Create New Agency User
          </div>
          <button class="modal-close" @click="showAddUserModal = false">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input v-model="newUser.name" type="text" class="form-input" placeholder="e.g. Mahbub Hasan" required />
          </div>
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input v-model="newUser.email" type="email" class="form-input" placeholder="e.g. mahbub@biswasit.com" required />
          </div>
          <div class="form-group">
            <label class="form-label">Assigned Role</label>
            <select v-model="newUser.role" class="form-input" style="background: #1e293b; color: #fff;">
              <option value="admin">Admin (Full Control)</option>
              <option value="team_member">Team Member (Engineering/Design)</option>
              <option value="client">Client (External Stakeholder)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-ghost" @click="showAddUserModal = false">Cancel</button>
          <button class="btn btn-primary" @click="addUser">✓ Create & Provision Account</button>
        </div>
      </div>
    </div>

    <!-- Live Telemetry Bar -->
    <div class="telemetry-bar animate-in">
      <div class="telemetry-item">
        <div class="pulse-indicator"></div>
        <div>
          <div style="font-size: 11px; color: var(--text-3); text-transform: uppercase; font-weight: 700;">System Telemetry</div>
          <div style="font-size: 13px; font-weight: 600; color: #fff;">Operational (99.98%)</div>
        </div>
      </div>
      <div class="telemetry-item">
        <div>
          <div style="font-size: 11px; color: var(--text-3);">CPU / Memory</div>
          <div style="font-size: 13px; font-weight: 600; color: #fff;">18% · 380MB</div>
        </div>
      </div>
      <div class="telemetry-item">
        <div>
          <div style="font-size: 11px; color: var(--text-3);">Database Response</div>
          <div style="font-size: 13px; font-weight: 600; color: #22c55e;">6.4 ms (Fast)</div>
        </div>
      </div>
      <div class="telemetry-item flex items-center gap-8 ml-auto">
        <button class="btn btn-ghost btn-sm" :disabled="cacheClearing" @click="clearCache">
          {{ cacheClearing ? 'Flushing...' : '⚡ Clear Cache' }}
        </button>
        <button
          class="btn btn-sm"
          :style="maintenanceMode ? 'background: #ef4444; color: #fff;' : 'background: rgba(255,255,255,0.06); color: var(--text-2);'"
          @click="toggleMaintenance"
        >
          {{ maintenanceMode ? '⚠️ Maintenance ON' : '🛡 Normal Mode' }}
        </button>
      </div>
    </div>

    <!-- Header -->
    <div class="page-header animate-in">
      <div class="flex items-center justify-between flex-wrap gap-12">
        <div>
          <div class="page-eyebrow">Executive Command Center · Biswas IT Firm</div>
          <h1 class="page-title">Agency Headquarters</h1>
          <p class="page-desc">System governance, RBAC security matrix, user provisioning, and agency financial performance.</p>
        </div>
        <div>
          <button class="btn btn-primary" @click="showAddUserModal = true">
            + Provision New User
          </button>
        </div>
      </div>
    </div>

    <!-- Sub Navigation Tabs (Allows clicking directly or from sidebar) -->
    <div class="tab-bar animate-in">
      <button :class="['tab-btn', { active: activeTab === 'dashboard' }]" @click="setTab('dashboard')">
        <span>⊞</span> Command Center
      </button>
      <button :class="['tab-btn', { active: activeTab === 'users' }]" @click="setTab('users')">
        <span>👥</span> User Management ({{ usersList.length }})
      </button>
      <button :class="['tab-btn', { active: activeTab === 'permissions' }]" @click="setTab('permissions')">
        <span>⚡</span> RBAC Permission Matrix
      </button>
      <button :class="['tab-btn', { active: activeTab === 'projects' }]" @click="setTab('projects')">
        <span>📁</span> Agency Projects
      </button>
      <button :class="['tab-btn', { active: activeTab === 'reports' }]" @click="setTab('reports')">
        <span>📊</span> Financial & Analytics
      </button>
      <button :class="['tab-btn', { active: activeTab === 'settings' }]" @click="setTab('settings')">
        <span>⚙️</span> System Settings
      </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: COMMAND CENTER (OVERVIEW)                               -->
    <!-- ============================================================== -->
    <div v-if="activeTab === 'dashboard'">
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

      <!-- Main Overview Grid -->
      <div class="grid grid-main animate-in delay-2">
        <!-- Left: Users & Matrix preview -->
        <div style="display: flex; flex-direction: column; gap: 20px;">

          <!-- User Directory Preview -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">👥 Agency Accounts</span>
              <button class="btn btn-ghost btn-sm" @click="setTab('users')">Manage All Users →</button>
            </div>
            <div class="panel-body table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Assigned Role</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="u in usersList.slice(0, 4)" :key="u.id">
                    <td style="font-weight: 600; color: var(--text);">{{ u.name }}</td>
                    <td style="color: var(--text-2); font-size: 13px;">{{ u.email }}</td>
                    <td>
                      <span :class="['badge', roleBadgeColor[u.role] ?? 'badge-blue']">
                        {{ u.role.replace('_', ' ') }}
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-ghost btn-sm" style="font-size: 11px; padding: 4px 8px;" @click="toggleUserRole(u)">
                        Switch Role ⟳
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Quick Matrix Preview -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">🔐 Live RBAC Security Capability</span>
              <button class="btn btn-ghost btn-sm" @click="setTab('permissions')">Inspect Full Matrix →</button>
            </div>
            <div class="panel-body table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Permission Name</th>
                    <th>Slug</th>
                    <th style="text-align: center;">Admin</th>
                    <th style="text-align: center;">Team</th>
                    <th style="text-align: center;">Client</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="p in permissions.slice(0, 5)" :key="p.slug">
                    <td style="font-weight: 600; color: var(--text);">{{ p.name }}</td>
                    <td style="font-family: monospace; font-size: 12px; color: var(--text-3);">{{ p.slug }}</td>
                    <td style="text-align: center;"><span class="perm-yes">✓</span></td>
                    <td style="text-align: center;">
                      <span :class="p.team ? 'perm-yes' : 'perm-no'">{{ p.team ? '✓' : '—' }}</span>
                    </td>
                    <td style="text-align: center;">
                      <span :class="p.client ? 'perm-yes' : 'perm-no'">{{ p.client ? '✓' : '—' }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <!-- Right: Activity & Governance Actions -->
        <div style="display: flex; flex-direction: column; gap: 20px;">

          <!-- Audit Activity Log -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">⚡ Security & Audit Log</span>
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

          <!-- Quick Governance Actions -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">🚀 Administrative Controls</span>
            </div>
            <div class="panel-body">
              <div class="quick-actions">
                <div class="quick-action" @click="showAddUserModal = true">
                  <div class="qa-icon">👤</div>
                  Add Agency User
                </div>
                <div class="quick-action" @click="setTab('projects')">
                  <div class="qa-icon">📁</div>
                  Manage Projects
                </div>
                <div class="quick-action" @click="setTab('permissions')">
                  <div class="qa-icon">🔐</div>
                  Role Matrix
                </div>
                <div class="quick-action" @click="setTab('reports')">
                  <div class="qa-icon">📊</div>
                  Export Reports
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 2: USER MANAGEMENT                                         -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'users'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <div class="flex items-center gap-12 flex-wrap">
            <span class="panel-title">👥 User Management & Access Provisioning</span>
            <input
              v-model="userSearch"
              type="text"
              placeholder="Search by name, email, or role..."
              class="form-input"
              style="width: 280px; padding: 6px 12px; font-size: 13px;"
            />
          </div>
          <button class="btn btn-primary btn-sm" @click="showAddUserModal = true">
            + Add New User
          </button>
        </div>
        <div class="panel-body table-wrap">
          <table>
            <thead>
              <tr>
                <th>User Name</th>
                <th>Email Address</th>
                <th>Current Role</th>
                <th>Status</th>
                <th>Member Since</th>
                <th style="text-align: right;">Role Operations</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in filteredUsers" :key="u.id">
                <td style="font-weight: 700; color: var(--text);">{{ u.name }}</td>
                <td style="color: var(--text-2);">{{ u.email }}</td>
                <td>
                  <span :class="['badge', roleBadgeColor[u.role] ?? 'badge-blue']">
                    {{ u.role.replace('_', ' ') }}
                  </span>
                </td>
                <td><span class="badge badge-green">{{ u.status }}</span></td>
                <td style="color: var(--text-3); font-size: 12px;">{{ u.joined }}</td>
                <td style="text-align: right;">
                  <button class="btn btn-ghost btn-sm" @click="toggleUserRole(u)">
                    Rotate Role ({{ u.role }}) ⟳
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 3: PERMISSION MATRIX                                       -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'permissions'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <div class="flex items-center gap-12 flex-wrap">
            <span class="panel-title">⚡ Interactive RBAC Permission Grid</span>
            <input
              v-model="permSearch"
              type="text"
              placeholder="Filter permissions..."
              class="form-input"
              style="width: 240px; padding: 6px 12px; font-size: 13px;"
            />
          </div>
          <div class="badge badge-green">Live Enforced via Middleware</div>
        </div>
        <div class="panel-body table-wrap">
          <table>
            <thead>
              <tr>
                <th>Permission & Slug</th>
                <th>Scope Description</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Team Member</th>
                <th style="text-align: center;">Client</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in filteredPermissions" :key="p.slug">
                <td>
                  <div style="font-weight: 700; color: var(--text);">{{ p.name }}</div>
                  <div style="font-family: monospace; font-size: 11px; color: var(--text-3);">{{ p.slug }}</div>
                </td>
                <td style="font-size: 12px; color: var(--text-2); max-width: 320px;">
                  {{ p.desc }}
                </td>
                <td style="text-align: center;">
                  <button
                    class="btn btn-ghost btn-sm"
                    :style="p.admin ? 'color: #22c55e;' : 'color: #64748b;'"
                    @click="togglePermission(p, 'admin')"
                  >
                    {{ p.admin ? '✓ Allowed' : '— Denied' }}
                  </button>
                </td>
                <td style="text-align: center;">
                  <button
                    class="btn btn-ghost btn-sm"
                    :style="p.team ? 'color: #22c55e;' : 'color: #64748b;'"
                    @click="togglePermission(p, 'team')"
                  >
                    {{ p.team ? '✓ Allowed' : '— Denied' }}
                  </button>
                </td>
                <td style="text-align: center;">
                  <button
                    class="btn btn-ghost btn-sm"
                    :style="p.client ? 'color: #22c55e;' : 'color: #64748b;'"
                    @click="togglePermission(p, 'client')"
                  >
                    {{ p.client ? '✓ Allowed' : '— Denied' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 4: AGENCY PROJECTS                                         -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'projects'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">📁 Agency Project Portfolio & Financials</span>
          <button class="btn btn-primary btn-sm" @click="showToast('📁 New Project wizard initiated')">+ New Agency Project</button>
        </div>
        <div class="panel-body table-wrap">
          <table>
            <thead>
              <tr>
                <th>Project Name</th>
                <th>Client</th>
                <th>Budget</th>
                <th>Burned / Spent</th>
                <th>Lead Architect</th>
                <th>Status</th>
                <th>Health</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="proj in agencyProjects" :key="proj.name">
                <td style="font-weight: 700; color: var(--text);">{{ proj.name }}</td>
                <td style="color: var(--text-2);">{{ proj.client }}</td>
                <td style="font-weight: 700; color: #fff;">{{ proj.budget }}</td>
                <td style="color: var(--admin-2);">{{ proj.spent }}</td>
                <td>{{ proj.lead }}</td>
                <td><span class="badge badge-blue">{{ proj.status }}</span></td>
                <td><span class="badge badge-green">{{ proj.health }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 5: FINANCIAL & ANALYTICS                                   -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'reports'" class="animate-in">
      <div class="grid grid-3" style="margin-bottom: 20px;">
        <div class="stat-card">
          <div class="stat-label">YTD Agency Revenue</div>
          <div class="stat-value">$182,400</div>
          <div class="stat-sub">+24% year-on-year growth</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Average Project Margin</div>
          <div class="stat-value" style="color: var(--green);">42.8%</div>
          <div class="stat-sub">Above industry benchmark</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Engineering Utilization</div>
          <div class="stat-value" style="color: var(--admin-2);">86.4%</div>
          <div class="stat-sub">Optimal billable velocity</div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">📊 Revenue Breakdown by Service Offering</span>
        </div>
        <div class="panel-body" style="display: flex; flex-direction: column; gap: 16px;">
          <div>
            <div class="progress-label">
              <span>Full-Stack Web Application Engineering</span>
              <strong>$98,000 (54%)</strong>
            </div>
            <div class="progress-track"><div class="progress-fill" style="width: 54%; background: var(--admin-grad);"></div></div>
          </div>
          <div>
            <div class="progress-label">
              <span>Cloud & DevOps Infrastructure Architecture</span>
              <strong>$42,000 (23%)</strong>
            </div>
            <div class="progress-track"><div class="progress-fill" style="width: 23%; background: #3b82f6;"></div></div>
          </div>
          <div>
            <div class="progress-label">
              <span>UI / UX Design Systems</span>
              <strong>$28,000 (15%)</strong>
            </div>
            <div class="progress-track"><div class="progress-fill" style="width: 15%; background: #eab308;"></div></div>
          </div>
          <div>
            <div class="progress-label">
              <span>AI Integration & Automation Solutions</span>
              <strong>$14,400 (8%)</strong>
            </div>
            <div class="progress-track"><div class="progress-fill" style="width: 8%; background: #22c55e;"></div></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 6: SYSTEM SETTINGS                                         -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'settings'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">⚙️ System & Security Configuration</span>
        </div>
        <div class="panel-body" style="display: flex; flex-direction: column; gap: 18px; max-width: 600px;">
          <div class="form-group">
            <label class="form-label">Agency Legal Name</label>
            <input type="text" class="form-input" value="Biswas IT Firm Ltd." />
          </div>
          <div class="form-group">
            <label class="form-label">Session Idle Timeout</label>
            <select class="form-input" style="background: #1e293b; color: #fff;">
              <option value="120">120 Minutes (Default)</option>
              <option value="60">60 Minutes</option>
              <option value="30">30 Minutes (High Security)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Brute Force Rate Limiting</label>
            <input type="text" class="form-input" value="5 requests per minute per IP" disabled style="opacity: 0.7;" />
          </div>
          <div>
            <button class="btn btn-primary" @click="showToast('✅ Configuration saved successfully.')">
              Save Settings
            </button>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>
