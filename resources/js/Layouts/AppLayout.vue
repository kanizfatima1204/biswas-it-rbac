<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page   = usePage();
const user   = computed(() => page.props.auth.user);
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

// Role-specific nav items
const navItems = computed(() => {
  const role = user.value?.role;
  if (role === 'admin') return [
    { href: '/dashboard',  label: 'Dashboard',        icon: '⊞' },
    { href: '/admin-only', label: 'Admin Control',    icon: '🔐' },
    { href: '#users',      label: 'User Management',  icon: '👥' },
    { href: '#projects',   label: 'Projects',         icon: '📁' },
    { href: '#reports',    label: 'Reports',          icon: '📊' },
    { href: '#settings',   label: 'Settings',         icon: '⚙️' },
  ];
  if (role === 'team_member') return [
    { href: '/dashboard', label: 'Dashboard',  icon: '⊞' },
    { href: '/team-only', label: 'Team Hub',   icon: '🔒' },
    { href: '#tasks',     label: 'My Tasks',   icon: '✅' },
    { href: '#projects',  label: 'Projects',   icon: '📁' },
    { href: '#calendar',  label: 'Calendar',   icon: '📅' },
  ];
  return [
    { href: '/dashboard', label: 'Dashboard',        icon: '⊞' },
    { href: '#projects',  label: 'My Projects',      icon: '📁' },
    { href: '#requests',  label: 'Service Requests', icon: '📩' },
    { href: '#invoices',  label: 'Invoices',         icon: '🧾' },
    { href: '#support',   label: 'Support',          icon: '💬' },
  ];
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
    <aside :class="['sidebar', { open: sidebarOpen }]">
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
        <Link
          v-for="item in navItems"
          :key="item.href"
          :href="item.href"
          :class="['nav-link', { active: item.href === '/dashboard' && page.url === '/dashboard' }]"
          @click="closeSidebar"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          {{ item.label }}
        </Link>
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
