<script setup>
import { computed, ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  activeTab: {
    type: String,
    default: 'dashboard',
  },
});

const emit = defineEmits(['tabChange']);

const page = usePage();
const user = computed(() => page.props.auth.user);
const sidebarOpen = ref(false);

const initials = computed(() => {
  if (!user.value?.name) return '?';
  return user.value.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
});

const roleLabel = computed(() => {
  const map = { admin: 'Admin', team_member: 'Team Member', client: 'Client' };
  return map[user.value?.role] ?? user.value?.role ?? '';
});

const roleClass = computed(() => {
  return { admin: '', team_member: 'team', client: 'client' }[user.value?.role] ?? '';
});

// Role-specific nav items with direct interactive IDs
const navItems = computed(() => {
  const role = user.value?.role;
  if (role === 'admin') return [
    { id: 'dashboard',   label: 'Command Center',    icon: '⊞' },
    { href: '/admin-only', label: 'Admin Security',  icon: '🔐' },
    { id: 'users',       label: 'User Management',   icon: '👥' },
    { id: 'permissions', label: 'Permission Matrix', icon: '⚡' },
    { id: 'projects',    label: 'Agency Projects',   icon: '📁' },
    { id: 'reports',     label: 'Financial Reports', icon: '📊' },
    { id: 'settings',    label: 'System Settings',   icon: '⚙️' },
  ];
  if (role === 'team_member') return [
    { id: 'dashboard', label: 'Agile Workspace',   icon: '⊞' },
    { href: '/team-only', label: 'Team Hub',       icon: '🔒' },
    { id: 'tasks',     label: 'My Tasks & Board',  icon: '✅' },
    { id: 'projects',  label: 'Active Projects',   icon: '📁' },
    { id: 'calendar',  label: 'Sprint Calendar',   icon: '📅' },
  ];
  return [
    { id: 'dashboard', label: 'Dashboard',         icon: '⊞' },
    { id: 'projects',  label: 'My Projects',       icon: '📁' },
    { id: 'requests',  label: 'Service Requests',  icon: '📩' },
    { id: 'invoices',  label: 'Invoices',          icon: '🧾' },
    { id: 'support',   label: 'Support',           icon: '💬' },
  ];
});

const isItemActive = (item) => {
  if (item.href) {
    return page.url === item.href;
  }
  return props.activeTab === item.id;
};

const handleNavClick = (item) => {
  if (item.href) {
    router.visit(item.href);
  } else if (item.id) {
    emit('tabChange', item.id);
    window.location.hash = item.id;
  }
  closeSidebar();
};

onMounted(() => {
  const hash = window.location.hash.replace('#', '');
  if (hash) {
    const valid = navItems.value.some(i => i.id === hash);
    if (valid) {
      emit('tabChange', hash);
    }
  }
});

const logout = () => router.post('/logout');
const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value; };
const closeSidebar  = () => { sidebarOpen.value = false; };
</script>

<template>
  <!-- Mobile Overlay -->
  <div :class="['overlay', { active: sidebarOpen }]" @click="closeSidebar"></div>

  <div class="shell">
    <!-- ── Sidebar ── -->
    <aside :class="['sidebar', roleClass, { open: sidebarOpen }]">
      <!-- Brand -->
      <div class="sidebar-top">
        <div class="brand">
          <div class="brand-icon">B</div>
          <div>
            <div class="brand-name">Biswas IT Firm</div>
            <div class="brand-tagline">Agency Management</div>
          </div>
        </div>

        <!-- User Card -->
        <div class="user-card" v-if="user">
          <div class="user-avatar">{{ initials }}</div>
          <div class="user-info">
            <div class="user-name">{{ user.name }}</div>
            <div class="user-email">{{ user.email }}</div>
            <div :class="['role-badge', roleClass]">
              <span class="role-dot"></span>
              {{ roleLabel }}
            </div>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav class="sidebar-nav">
        <div class="nav-section-label">Navigation</div>
        <button
          v-for="item in navItems"
          :key="item.id || item.href"
          type="button"
          :class="['nav-link', { active: isItemActive(item) }]"
          style="background: transparent; border: none;"
          @click="handleNavClick(item)"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </button>
      </nav>

      <!-- Footer -->
      <div class="sidebar-footer">
        <div class="security-badge">
          <span class="security-badge-icon">🔒</span>
          RBAC Protected Session
        </div>
        <button class="logout-btn" @click="logout">
          <span>🚪</span>
          Sign out
        </button>
      </div>
    </aside>

    <!-- ── Main ── -->
    <div class="main">
      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="toggleSidebar" aria-label="Toggle menu">
            ☰
          </button>
          <div>
            <div class="topbar-greeting">Welcome back 👋</div>
            <div class="topbar-title">{{ user?.name }}</div>
          </div>
        </div>
        <div class="topbar-right">
          <div :class="['role-badge', roleClass]">
            <span class="role-dot"></span>
            {{ roleLabel }}
          </div>
          <button class="btn btn-ghost btn-sm" @click="logout">Sign out</button>
        </div>
      </header>

      <!-- Page Content -->
      <section class="content">
        <slot />
      </section>
    </div>
  </div>
</template>
