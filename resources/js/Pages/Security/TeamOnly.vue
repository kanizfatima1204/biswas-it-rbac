<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  message: {
    type: String,
    default: 'Team permission confirmed',
  },
});
</script>

<template>
  <AppLayout>
    <div style="max-width: 760px; margin: 40px auto; text-align: center;" class="animate-in">
      <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(34,197,94,0.12); border: 2px solid #22c55e; display: grid; place-items: center; font-size: 36px; margin: 0 auto 20px; box-shadow: 0 0 30px rgba(34,197,94,0.3);">
        🛡️
      </div>

      <div class="page-eyebrow" style="background: var(--team-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
        RBAC Route Protection Active
      </div>
      <h1 class="page-title" style="margin-bottom: 12px;">Team Security Gate Verified</h1>
      <p class="page-desc" style="max-width: 540px; margin: 0 auto 28px;">
        {{ message }} — You have successfully passed the backend <code>RoleMiddleware</code> gate protecting <code>/team-only</code>.
      </p>

      <div class="panel" style="text-align: left; margin-bottom: 28px; border-top: 3px solid var(--team-1);">
        <div class="panel-header">
          <span class="panel-title">🔒 Route Verification Details</span>
          <span class="badge badge-green">HTTP 200 OK</span>
        </div>
        <div class="panel-body" style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Target Route:</span>
            <code style="background: rgba(255,255,255,0.06); padding: 2px 8px; border-radius: 4px; color: var(--team-2);">GET /team-only</code>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Middleware Guard:</span>
            <code>role:admin,team_member</code>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Client Access Status:</span>
            <span class="badge badge-red">HTTP 403 Forbidden</span>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Team Access Status:</span>
            <span class="badge badge-green">Authorized & Granted</span>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-center gap-12">
        <Link href="/dashboard" class="btn btn-primary" style="background: var(--team-grad); color: #fff; font-weight: 600;">
          ← Back to Agile Workspace
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
