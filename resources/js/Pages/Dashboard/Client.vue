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

// Toast notification state
const toastMsg = ref('');
const showToast = (msg) => {
  toastMsg.value = msg;
  setTimeout(() => { toastMsg.value = ''; }, 4500);
};

// Interactive Modal State for "+ Submit Request"
const showRequestModal = ref(false);
const submitting = ref(false);
const newRequest = ref({
  project: 'E-Commerce Platform',
  title: '',
  category: 'Feature Request',
  priority: 'Normal',
  details: '',
});

// Dynamic Requests List
const requestsList = ref([
  { id: 'SR-104', title: 'Payment Gateway Stripe Integration', project: 'E-Commerce Platform', category: 'Feature Request', priority: 'High', status: 'in_progress', date: 'Yesterday' },
  { id: 'SR-102', title: 'SEO Meta Tags & Sitemap Generation', project: 'Corporate Website', category: 'SEO Tweak', priority: 'Normal', status: 'review', date: 'Sep 15' },
  { id: 'SR-098', title: 'SSL Certificate & Domain Pointing', project: 'Corporate Website', category: 'DevOps', priority: 'Critical', status: 'completed', date: 'Sep 10' },
]);

const openRequestsCount = computed(() => {
  return requestsList.value.filter(r => r.status !== 'completed').length;
});

const submitServiceRequest = () => {
  if (!newRequest.value.title.trim()) {
    alert('Please enter a request title.');
    return;
  }
  submitting.value = true;
  setTimeout(() => {
    requestsList.value.unshift({
      id: `SR-${Math.floor(105 + Math.random() * 90)}`,
      title: newRequest.value.title,
      project: newRequest.value.project,
      category: newRequest.value.category,
      priority: newRequest.value.priority,
      status: 'pending',
      date: 'Just now',
    });
    submitting.value = false;
    showRequestModal.value = false;
    showToast(`✅ Service Request "${newRequest.value.title}" submitted successfully! Our agency team is on it.`);
    newRequest.value.title = '';
    newRequest.value.details = '';
  }, 400);
};

// Interactive Support Chat State
const chatMessages = ref([
  { sender: 'agent', name: 'Tanvir (Senior PM)', text: 'Hello! I am your dedicated account manager at Biswas IT Firm. How can we assist your projects today?', time: '10:15 AM' },
]);
const chatInput = ref('');
const isAgentTyping = ref(false);

const sendChatMessage = () => {
  if (!chatInput.value.trim()) return;
  const msg = chatInput.value;
  chatMessages.value.push({
    sender: 'client',
    name: user.value?.name ?? 'You',
    text: msg,
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
  });
  chatInput.value = '';

  isAgentTyping.value = true;
  setTimeout(() => {
    isAgentTyping.value = false;
    chatMessages.value.push({
      sender: 'agent',
      name: 'Tanvir (Senior PM)',
      text: 'Thank you for your note! We have logged this directly into our agency sprint queue. You will receive an update shortly.',
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    });
  }, 1200);
};

// Invoices data
const invoices = ref([
  { id: 'INV-2026-089', date: 'Sep 01, 2026', project: 'E-Commerce Platform (Milestone 2)', amount: '$2,500.00', status: 'paid' },
  { id: 'INV-2026-074', date: 'Aug 15, 2026', project: 'Corporate Website (Final Phase)', amount: '$1,800.00', status: 'paid' },
  { id: 'INV-2026-061', date: 'Jul 28, 2026', project: 'UI/UX Design Sprint', amount: '$1,200.00', status: 'paid' },
  { id: 'INV-2026-095', date: 'Sep 20, 2026', project: 'E-Commerce Platform (Milestone 3)', amount: '$2,500.00', status: 'pending' },
]);

const downloadInvoice = (inv) => {
  showToast(`📄 Downloading official invoice receipt ${inv.id} ($${inv.amount})...`);
};

const statusClass = {
  in_progress: 'badge-blue',
  review:      'badge-yellow',
  completed:   'badge-green',
  pending:     'badge-yellow',
  paid:        'badge-green',
};
const statusLabel = {
  in_progress: '⚙ In Progress',
  review:      '🔍 In Review',
  completed:   '✓ Completed',
  pending:     '⏳ Pending Review',
};

const statCards = computed(() => [
  { icon: '📁', label: 'Active Engagements', value: props.stats.projects ?? 2, sub: 'Sprint milestone active', change: '2 projects', dir: 'up' },
  { icon: '📩', label: 'Open Service Tickets', value: openRequestsCount.value, sub: 'Agency queue', change: 'Live monitored', dir: 'up' },
  { icon: '📦', label: 'Approved Deliverables', value: props.stats.completed_deliverables ?? 7, sub: 'Assets ready', change: '+2 this month', dir: 'up' },
  { icon: '👑', label: 'Client Tier', value: 'VIP Gold', sub: '24/7 Priority SLA', change: 'Guaranteed 24h', dir: 'up' },
]);
</script>

<template>
  <AppLayout :activeTab="activeTab" @tabChange="setTab">

    <!-- Toast Notification -->
    <div v-if="toastMsg" class="toast-notice">
      <span>{{ toastMsg }}</span>
    </div>

    <!-- Modal: New Service Request -->
    <div v-if="showRequestModal" class="modal-overlay" @click.self="showRequestModal = false">
      <div class="modal-dialog">
        <div class="modal-header">
          <div class="modal-title">
            <span style="font-size: 20px;">📩</span>
            Submit New Service Request
          </div>
          <button class="modal-close" @click="showRequestModal = false">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Related Project</label>
            <select v-model="newRequest.project" class="form-input" style="background: #1e293b; color: #fff;">
              <option value="E-Commerce Platform">E-Commerce Platform</option>
              <option value="Corporate Website">Corporate Website</option>
              <option value="Mobile App v2">Mobile App v2 (Upcoming)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Request Title *</label>
            <input
              v-model="newRequest.title"
              type="text"
              class="form-input"
              placeholder="e.g. Add multi-currency support to checkout"
              required
            />
          </div>

          <div class="grid grid-2" style="gap: 12px;">
            <div class="form-group">
              <label class="form-label">Category</label>
              <select v-model="newRequest.category" class="form-input" style="background: #1e293b; color: #fff;">
                <option value="Feature Request">New Feature</option>
                <option value="UI/UX Tweak">UI / UX Design</option>
                <option value="Bug Fix">Bug / Issue Fix</option>
                <option value="SEO & Performance">Performance / SEO</option>
                <option value="Consultation">Strategy Consultation</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Priority Level</label>
              <select v-model="newRequest.priority" class="form-input" style="background: #1e293b; color: #fff;">
                <option value="Normal">Normal (24-48 hours)</option>
                <option value="High">High (Within 24 hours)</option>
                <option value="Critical">Critical (Emergency Response)</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Request Details & Specifications</label>
            <textarea
              v-model="newRequest.details"
              rows="3"
              class="form-input"
              placeholder="Describe requirements, expected results, or link design assets..."
              style="resize: vertical;"
            ></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-ghost" @click="showRequestModal = false">Cancel</button>
          <button
            class="btn"
            style="background: var(--client-grad); color: #fff; font-weight: 600; box-shadow: 0 4px 14px rgba(217,119,6,0.4);"
            :disabled="submitting"
            @click="submitServiceRequest"
          >
            {{ submitting ? 'Submitting...' : '✓ Confirm & Submit Request' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Header -->
    <div class="page-header animate-in">
      <div class="flex items-center justify-between flex-wrap gap-12">
        <div>
          <div class="page-eyebrow" style="background: var(--client-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
            VIP Client Portal · Biswas IT Firm
          </div>
          <h1 class="page-title">Executive Suite</h1>
          <p class="page-desc">Track project milestones, review deliverables, manage billing, and submit service tickets.</p>
        </div>
        <div>
          <!-- Working Submit Request Button -->
          <button
            class="btn"
            style="background: var(--client-grad); color: #fff; font-weight: 700; padding: 10px 20px; box-shadow: 0 4px 16px rgba(217,119,6,0.45);"
            @click="showRequestModal = true"
          >
            + New Service Request
          </button>
        </div>
      </div>
    </div>

    <!-- Sub Navigation Tabs (Allows clicking or sidebar clicking) -->
    <div class="tab-bar animate-in">
      <button :class="['tab-btn', { active: activeTab === 'dashboard' }]" @click="setTab('dashboard')">
        <span>⊞</span> Overview
      </button>
      <button :class="['tab-btn', { active: activeTab === 'projects' }]" @click="setTab('projects')">
        <span>📁</span> My Projects ({{ stats.projects ?? 2 }})
      </button>
      <button :class="['tab-btn', { active: activeTab === 'requests' }]" @click="setTab('requests')">
        <span>📩</span> Service Requests ({{ requestsList.length }})
      </button>
      <button :class="['tab-btn', { active: activeTab === 'invoices' }]" @click="setTab('invoices')">
        <span>🧾</span> Invoices & Billing
      </button>
      <button :class="['tab-btn', { active: activeTab === 'support' }]" @click="setTab('support')">
        <span>💬</span> Dedicated Support
      </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: OVERVIEW                                                -->
    <!-- ============================================================== -->
    <div v-if="activeTab === 'dashboard'">
      <!-- Stat Cards -->
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

      <!-- Main Overview Grid -->
      <div class="grid grid-main animate-in delay-2">
        <div style="display: flex; flex-direction: column; gap: 20px;">

          <!-- Milestone Stepper Timeline -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">🚀 Project Lifecycle & Milestone Tracker</span>
              <span class="badge badge-yellow">E-Commerce Platform</span>
            </div>
            <div class="panel-body">
              <div class="stepper-timeline">
                <div class="stepper-line"></div>
                <div class="stepper-line-active" style="width: 50%;"></div>

                <div class="stepper-node">
                  <div class="node-circle done">✓</div>
                  <div class="node-label">Discovery</div>
                </div>
                <div class="stepper-node">
                  <div class="node-circle done">✓</div>
                  <div class="node-label">UI/UX Prototype</div>
                </div>
                <div class="stepper-node">
                  <div class="node-circle current">3</div>
                  <div class="node-label" style="color: var(--client-2);">Development</div>
                </div>
                <div class="stepper-node">
                  <div class="node-circle">4</div>
                  <div class="node-label">QA Testing</div>
                </div>
                <div class="stepper-node">
                  <div class="node-circle">5</div>
                  <div class="node-label">Production Launch</div>
                </div>
              </div>
              <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 14px; font-size: 13px; color: var(--text-2);">
                <strong style="color: var(--text);">Current Stage:</strong> Milestone 3 Backend Engineering & Payment Integration. Next review scheduled for <strong>Oct 02, 2026</strong>.
              </div>
            </div>
          </div>

          <!-- Active Projects List -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">📁 Active Projects</span>
              <button class="btn btn-ghost btn-sm" @click="setTab('projects')">View All →</button>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 16px;">
              <div
                v-for="proj in stats.projects_list"
                :key="proj.name"
                style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; transition: all 0.2s; background: rgba(255,255,255,0.02);"
              >
                <div class="flex items-center gap-8 mb-4">
                  <span style="font-size: 15px; font-weight: 700; color: var(--text);">{{ proj.name }}</span>
                  <span :class="['badge', statusClass[proj.status]]" style="margin-left: auto;">
                    {{ statusLabel[proj.status] }}
                  </span>
                </div>
                <div class="progress-label" style="margin-bottom: 6px;">
                  <span style="color: var(--text-3); font-size: 12px;">Milestone Progress</span>
                  <span style="font-weight: 700; color: var(--client-2);">{{ proj.progress }}%</span>
                </div>
                <div class="progress-track">
                  <div class="progress-fill" :style="`width: ${proj.progress}%; background: var(--client-grad)`"></div>
                </div>
                <div class="flex items-center justify-between mt-8 text-xs text-muted">
                  <span>📅 Target Release: <strong style="color: var(--text-2);">{{ proj.deadline }}</strong></span>
                  <span style="color: var(--client-2); cursor: pointer;" @click="setTab('projects')">Inspect Specifications & Checklist →</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Deliverables Vault -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">📦 Project Deliverables Vault</span>
              <span class="badge badge-green">Ready for Download</span>
            </div>
            <div class="panel-body">
              <div class="activity-list">
                <div class="activity-item" v-for="(d, i) in stats.deliverables" :key="i">
                  <div class="activity-dot" :style="d.status === 'pending' ? 'background: var(--yellow)' : ''"></div>
                  <div style="flex: 1;">
                    <div class="activity-text">
                      <strong>{{ d.title }}</strong>
                    </div>
                    <div class="flex items-center gap-8 mt-4">
                      <span class="activity-time">{{ d.date }}</span>
                      <span :class="['badge', d.status === 'delivered' ? 'badge-green' : 'badge-yellow']" style="font-size: 10px;">
                        {{ d.status === 'delivered' ? '✓ Delivered' : '⏳ In Progress' }}
                      </span>
                    </div>
                  </div>
                  <button
                    v-if="d.status === 'delivered'"
                    class="btn btn-ghost btn-sm"
                    style="font-size: 11px; padding: 4px 10px;"
                    @click="showToast(`📥 Downloading deliverable asset package for: ${d.title}`)"
                  >
                    Download 📥
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: 20px;">

          <!-- Working Service Request CTA -->
          <div class="request-card">
            <div style="font-size: 36px; margin-bottom: 8px;">📩</div>
            <h3 style="font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px;">New Service Request</h3>
            <p style="font-size: 13px; color: var(--text-2); margin-bottom: 18px;">
              Need a new feature, design iteration, or urgent adjustment? Submit a ticket and our lead engineer will respond within 24 hours.
            </p>
            <!-- THIS BUTTON WORKS! -->
            <button
              id="submit-request-btn"
              class="btn btn-full"
              style="background: var(--client-grad); color: #fff; font-weight: 700; box-shadow: 0 4px 16px rgba(217,119,6,0.45); cursor: pointer;"
              @click="showRequestModal = true"
            >
              + Submit Request
            </button>
          </div>

          <!-- Dedicated PM Card -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">👤 Dedicated Project Manager</span>
            </div>
            <div class="panel-body">
              <div class="flex items-center gap-12 mb-12">
                <div class="user-avatar" style="background: var(--client-grad); width: 44px; height: 44px; font-size: 16px;">
                  TI
                </div>
                <div>
                  <div style="font-weight: 700; color: var(--text);">Tanvir Islam</div>
                  <div style="font-size: 12px; color: var(--text-3);">Lead Solutions Architect & PM</div>
                </div>
              </div>
              <div style="font-size: 12px; color: var(--text-2); display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                <div>📞 Direct Line: <strong>+880 1700-000000</strong></div>
                <div>✉️ Email: <strong>tanvir@biswasit.com</strong></div>
                <div>⏰ Office Hours: <strong>09:00 AM - 06:00 PM (GMT+6)</strong></div>
              </div>
              <button class="btn btn-full btn-ghost btn-sm" @click="setTab('support')">
                Open Direct Chat 💬
              </button>
            </div>
          </div>

          <!-- Access Level Security Card -->
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">🔒 Role Security Status</span>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--text-2);">
              <div>✅ <strong style="color:var(--text)">projects.view</strong> — Scoped to your firm records</div>
              <div>✅ <strong style="color:var(--text)">client.requests</strong> — Authorized to dispatch tickets</div>
              <div style="opacity: 0.4;">✗ <span style="text-decoration: line-through">admin.access</span> — Strictly protected</div>
              <div style="opacity: 0.4;">✗ <span style="text-decoration: line-through">tasks.assign</span> — Team internal</div>
              <div style="margin-top: 10px; padding: 10px; background: rgba(217,119,6,0.08); border: 1px solid rgba(217,119,6,0.2); border-radius: 8px; color: var(--client-2); font-size: 12px;">
                🔐 Authenticated under Biswas IT Firm RBAC Session Protocol
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 2: MY PROJECTS                                             -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'projects'" class="animate-in">
      <div class="grid grid-2" style="gap: 20px;">
        <div class="panel" style="border-top: 3px solid var(--client-1);">
          <div class="panel-header">
            <div>
              <div class="panel-title">🛍️ E-Commerce Platform</div>
              <div style="font-size: 12px; color: var(--text-3); margin-top: 2px;">Contract #BIT-2026-042 · Full Stack Storefront</div>
            </div>
            <span class="badge badge-blue">In Progress (65%)</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 14px;">
            <p style="font-size: 13px; color: var(--text-2);">
              Enterprise digital commerce system featuring multi-vendor inventory, automated invoicing, and responsive mobile architecture.
            </p>
            <div class="progress-track">
              <div class="progress-fill" style="width: 65%; background: var(--client-grad);"></div>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">Vue 3 SPA</span>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">Laravel 12 REST</span>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">Stripe Gateway</span>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">PostgreSQL</span>
            </div>
            <div style="border-top: 1px solid var(--border); padding-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
              <span>Target Delivery: <strong style="color: #fff;">Oct 15, 2026</strong></span>
              <button class="btn btn-ghost btn-sm" @click="showToast('📄 Project specification PDF generated')">View Scope Doc</button>
            </div>
          </div>
        </div>

        <div class="panel" style="border-top: 3px solid var(--green);">
          <div class="panel-header">
            <div>
              <div class="panel-title">🌐 Corporate Brand Portal</div>
              <div style="font-size: 12px; color: var(--text-3); margin-top: 2px;">Contract #BIT-2026-018 · UI/UX & Web Presence</div>
            </div>
            <span class="badge badge-yellow">Final QA Review (90%)</span>
          </div>
          <div class="panel-body" style="display: flex; flex-direction: column; gap: 14px;">
            <p style="font-size: 13px; color: var(--text-2);">
              High-performance interactive marketing portal with headless CMS integration, animations, and lead generation funnel.
            </p>
            <div class="progress-track">
              <div class="progress-fill" style="width: 90%; background: var(--green);"></div>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">Next.js SSR</span>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">Tailwind CSS</span>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-2); font-size: 11px;">Framer Motion</span>
            </div>
            <div style="border-top: 1px solid var(--border); padding-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
              <span>Target Delivery: <strong style="color: #fff;">Sep 28, 2026</strong></span>
              <button class="btn btn-ghost btn-sm" @click="showToast('🔍 Opening staging preview environment...')">Staging Preview ↗</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 3: SERVICE REQUESTS                                        -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'requests'" class="animate-in">
      <div class="panel">
        <div class="panel-header">
          <div>
            <span class="panel-title">📩 Service Request Ticket Queue</span>
            <div style="font-size: 12px; color: var(--text-3); margin-top: 2px;">Track changes, bug fixes, and feature submissions</div>
          </div>
          <button
            class="btn"
            style="background: var(--client-grad); color: #fff; font-weight: 600; font-size: 13px; padding: 8px 16px;"
            @click="showRequestModal = true"
          >
            + Submit New Ticket
          </button>
        </div>
        <div class="panel-body table-wrap">
          <table>
            <thead>
              <tr>
                <th>Ticket ID</th>
                <th>Request Title</th>
                <th>Project</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Submitted</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="req in requestsList" :key="req.id">
                <td style="font-weight: 700; color: var(--client-2); font-family: monospace;">{{ req.id }}</td>
                <td style="font-weight: 600; color: var(--text);">{{ req.title }}</td>
                <td style="color: var(--text-2);">{{ req.project }}</td>
                <td><span class="badge" style="background: rgba(255,255,255,0.06); color: var(--text-2);">{{ req.category }}</span></td>
                <td>
                  <span :class="['badge', req.priority === 'Critical' ? 'badge-red' : req.priority === 'High' ? 'badge-yellow' : 'badge-blue']">
                    {{ req.priority }}
                  </span>
                </td>
                <td>
                  <span :class="['badge', statusClass[req.status]]">
                    {{ statusLabel[req.status] ?? req.status }}
                  </span>
                </td>
                <td style="color: var(--text-3); font-size: 12px;">{{ req.date }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 4: INVOICES & BILLING                                      -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'invoices'" class="animate-in">
      <div class="grid grid-3" style="margin-bottom: 20px;">
        <div class="stat-card">
          <div class="stat-label">Total Contract Billed</div>
          <div class="stat-value">$8,000.00</div>
          <div class="stat-sub">Across 2 project milestones</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Settled Payments</div>
          <div class="stat-value" style="color: var(--green);">$5,500.00</div>
          <div class="stat-sub">Verified via wire transfer</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Upcoming Due</div>
          <div class="stat-value" style="color: var(--yellow);">$2,500.00</div>
          <div class="stat-sub">Due upon Milestone 3 signoff</div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">🧾 Agency Billing Ledger</span>
        </div>
        <div class="panel-body table-wrap">
          <table>
            <thead>
              <tr>
                <th>Invoice #</th>
                <th>Issue Date</th>
                <th>Milestone Description</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th style="text-align: right;">Receipt</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inv in invoices" :key="inv.id">
                <td style="font-weight: 700; color: var(--text); font-family: monospace;">{{ inv.id }}</td>
                <td>{{ inv.date }}</td>
                <td style="font-weight: 600; color: var(--text);">{{ inv.project }}</td>
                <td style="font-weight: 700; color: var(--client-2);">{{ inv.amount }}</td>
                <td>
                  <span :class="['badge', inv.status === 'paid' ? 'badge-green' : 'badge-yellow']">
                    {{ inv.status === 'paid' ? '✓ Paid' : '⏳ Due' }}
                  </span>
                </td>
                <td style="text-align: right;">
                  <button class="btn btn-ghost btn-sm" @click="downloadInvoice(inv)">PDF 📥</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 5: DEDICATED SUPPORT & CHAT                                -->
    <!-- ============================================================== -->
    <div v-else-if="activeTab === 'support'" class="animate-in">
      <div class="grid grid-main">
        <div class="panel">
          <div class="panel-header">
            <div class="flex items-center gap-8">
              <span class="panel-title">💬 Priority Concierge Chat</span>
              <span class="badge badge-green">Online</span>
            </div>
            <div style="font-size: 12px; color: var(--text-3);">Biswas IT Client Operations</div>
          </div>
          <div class="chat-window">
            <div class="chat-msgs">
              <div
                v-for="(msg, i) in chatMessages"
                :key="i"
                :class="['chat-bubble', msg.sender]"
              >
                <div style="font-size: 11px; font-weight: 700; opacity: 0.8; margin-bottom: 2px;">
                  {{ msg.name }} · {{ msg.time }}
                </div>
                <div>{{ msg.text }}</div>
              </div>
              <div v-if="isAgentTyping" class="chat-bubble agent" style="font-style: italic; opacity: 0.7;">
                Tanvir is typing an update...
              </div>
            </div>
            <form @submit.prevent="sendChatMessage" class="chat-input-bar">
              <input
                v-model="chatInput"
                type="text"
                placeholder="Type your message or inquiry for the agency PM..."
                class="form-input"
                style="flex: 1;"
              />
              <button
                type="submit"
                class="btn"
                style="background: var(--client-grad); color: #fff; font-weight: 600;"
              >
                Send ✈
              </button>
            </form>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div class="panel">
            <div class="panel-header">
              <span class="panel-title">📞 Emergency Hotline</span>
            </div>
            <div class="panel-body" style="font-size: 13px; color: var(--text-2); display: flex; flex-direction: column; gap: 10px;">
              <p>For urgent production outages or critical escalations outside normal business hours:</p>
              <div style="padding: 12px; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25); border-radius: var(--radius-sm); color: #fca5a5; font-weight: 600;">
                🚨 24/7 Hotline: +880 1700-999999
              </div>
              <div style="font-size: 12px; color: var(--text-3);">
                Average response time for VIP Gold tier is under 15 minutes.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>
