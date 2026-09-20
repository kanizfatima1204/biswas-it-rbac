<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '', remember: false });

const submit = () => form.post('/login');

const quickLogin = (email) => {
  form.email    = email;
  form.password = 'password';
  form.post('/login');
};

const demoAccounts = [
  { label: 'Admin',       email: 'admin@biswasit.test', type: 'admin',
    desc: 'Full system access + permission management' },
  { label: 'Team Member', email: 'team@biswasit.test',  type: 'team',
    desc: 'Task & project access only' },
  { label: 'Client',      email: 'client@biswasit.test',type: 'client',
    desc: 'Project view & service requests' },
];
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <!-- Brand -->
      <div class="login-brand">
        <div class="login-brand-icon">B</div>
        <div>
          <div class="login-brand-name">Biswas IT Firm</div>
        </div>
      </div>
      <p class="login-subtitle">Role-Based Dashboard — Sign in to your account</p>

      <!-- Login Form -->
      <form class="login-form" @submit.prevent="submit">
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            placeholder="you@biswasit.test"
            autocomplete="email"
            required
          >
          <div class="form-error" v-if="form.errors.email">⚠ {{ form.errors.email }}</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-input"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          >
        </div>

        <label class="remember-row">
          <input v-model="form.remember" type="checkbox">
          Keep me signed in
        </label>

        <button class="btn btn-primary btn-full" :disabled="form.processing">
          {{ form.processing ? 'Signing in…' : '→ Sign In' }}
        </button>
      </form>

      <!-- Demo Quick Login -->
      <div class="divider">Quick Demo Login</div>

      <div class="demo-accounts">
        <button
          v-for="account in demoAccounts"
          :key="account.email"
          class="demo-account-btn"
          :disabled="form.processing"
          @click="quickLogin(account.email)"
          type="button"
        >
          <span :class="['demo-dot', account.type]"></span>
          <div>
            <div class="demo-role">{{ account.label }}</div>
            <div class="demo-cred">{{ account.desc }}</div>
          </div>
          <span style="margin-left:auto; color: var(--text-3); font-size: 12px;">→</span>
        </button>
      </div>
    </div>
  </div>
</template>
