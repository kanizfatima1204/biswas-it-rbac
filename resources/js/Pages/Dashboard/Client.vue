<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ stats: Object });
const page  = usePage();
const user  = computed(() => page.props.auth.user);

const statusClass = {
  in_progress: 'badge-blue',
  review:      'badge-yellow',
  completed:   'badge-green',
  on_hold:     'badge-red',
};
const statusLabel = {
  in_progress: '⚙ In Progress',
  review:      '🔍 In Review',
  completed:   '✓ Completed',
  on_hold:     '⏸ On Hold',
};

const deliveryStatus = {
  delivered: 'badge-green',
  pending:   'badge-yellow',
};

const statCards = computed(() => [
  { icon: '📁', label: 'My Projects',         value: props.stats.projects,               sub: 'Active engagements',         change: '2 active',      dir: 'up' },
  { icon: '📩', label: 'Open Requests',        value: props.stats.open_requests,          sub: 'Support tickets',            change: 'Being handled',  dir: 'up' },
  { icon: '📦', label: 'Deliverables',         value: props.stats.completed_deliverables, sub: 'Items received',             change: '+2 this month',  dir: 'up' },
  { icon: '✅', label: 'Account',              value: props.stats.account_status,         sub: 'Subscription status',        change: 'All services ok',dir: 'up' },
]);
</script>

<template>
  <AppLayout>
    <!-- Header -->
    <div class="page-header animate-in">
      <div class="page-eyebrow" style="background: var(--client-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
        Client Portal
      </div>
      <h1 class="page-title">My Services</h1>
      <p class="page-desc">Track your projects, review deliverables, and submit new service requests.</p>
    </div>

    <!-- Stat Cards (amber accent) -->
    <div class="grid grid-4 animate-in delay-1" style="margin-bottom: 24px;">
      <div class="stat-card" v-for="(card, i) in statCards" :key="i">
        <div class="stat-icon" style="background: var(--client-grad); box-shadow: 0 4px 12px rgba(217,119,6,0.35);">
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
      <!-- Left Column -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Projects -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">📁 My Projects</span>
            <span class="badge badge-yellow">{{ stats.projects }} active</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 18px;">
            <div v-for="proj in stats.projects_list" :key="proj.name"
                 style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; transition: all 0.2s;"
                 @mouseenter="e => e.currentTarget.style.borderColor='var(--border-h)'"
                 @mouseleave="e => e.currentTarget.style.borderColor='var(--border)'"
            >
              <div class="flex items-center gap-8 mb-4">
                <span style="font-size: 15px; font-weight: 700; color: var(--text);">{{ proj.name }}</span>
                <span :class="['badge', statusClass[proj.status]]" style="margin-left: auto;">
                  {{ statusLabel[proj.status] }}
                </span>
              </div>
              <div class="progress-label" style="margin-bottom: 6px;">
                <span style="color: var(--text-3); font-size: 12px;">Progress</span>
                <span style="font-weight: 700; color: var(--client-2);">{{ proj.progress }}%</span>
              </div>
              <div class="progress-track">
                <div
                  class="progress-fill"
                  :style="`width: ${proj.progress}%; background: var(--client-grad)`"
                ></div>
              </div>
              <div style="font-size: 12px; color: var(--text-3); margin-top: 8px;">
                📅 Deadline: <strong style="color: var(--text-2);">{{ proj.deadline }}</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- Deliverables Timeline -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">📦 Deliverables</span>
          </div>
          <div class="panel-body">
            <div class="activity-list">
              <div class="activity-item" v-for="(d, i) in stats.deliverables" :key="i">
                <div class="activity-dot" :style="d.status === 'pending' ? 'background: var(--yellow)' : ''"></div>
                <div>
                  <div class="activity-text">
                    <strong>{{ d.title }}</strong>
                  </div>
                  <div class="flex items-center gap-8 mt-4">
                    <span class="activity-time">{{ d.date }}</span>
                    <span :class="['badge', deliveryStatus[d.status]]" style="font-size: 10px;">
                      {{ d.status === 'delivered' ? '✓ Delivered' : '⏳ Pending' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Service Request CTA -->
        <div class="request-card">
          <div style="font-size: 32px; margin-bottom: 8px;">📩</div>
          <h3>New Service Request</h3>
          <p>Need something new? Submit a request and our team will get back to you within 24 hours.</p>
          <button class="btn btn-full" style="background: var(--client-grad); color: #fff; box-shadow: 0 4px 12px rgba(217,119,6,0.4);">
            + Submit Request
          </button>
        </div>

        <!-- Support Card -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">💬 Support</span>
            <span class="badge badge-green">Online</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 8px;">
            <div class="quick-action">
              <div class="qa-icon">💬</div>
              Live Chat Support
            </div>
            <div class="quick-action">
              <div class="qa-icon">📧</div>
              Email Support
            </div>
            <div class="quick-action">
              <div class="qa-icon">📞</div>
              Schedule a Call
            </div>
          </div>
        </div>

        <!-- Access Level Info -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">🔒 Your Access Level</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--text-2);">
            <div>✅ <strong style="color:var(--text)">projects.view</strong> — View your projects</div>
            <div>✅ <strong style="color:var(--text)">requests.create</strong> — Submit service requests</div>
            <div style="opacity: 0.4;">✗ <span style="text-decoration: line-through">admin.access</span> — Admin only</div>
            <div style="opacity: 0.4;">✗ <span style="text-decoration: line-through">tasks.manage</span> — Team only</div>
            <div style="margin-top: 12px; padding: 10px; background: rgba(217,119,6,0.08); border: 1px solid rgba(217,119,6,0.2); border-radius: 8px; color: var(--client-2); font-size: 12px;">
              🔐 Protected by session authentication + RBAC middleware
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
