<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  message: {
    type: String,
    default: 'Admin permission confirmed',
  },
});
</script>

<template>
  <AppLayout>
    <div style="max-width: 760px; margin: 40px auto; text-align: center;" class="animate-in">
      <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(124,58,237,0.15); border: 2px solid var(--admin-2); display: grid; place-items: center; font-size: 36px; margin: 0 auto 20px; box-shadow: 0 0 30px rgba(124,58,237,0.35);">
        🔐
      </div>

      <div class="page-eyebrow">
        Root Privilege Verified · Biswas IT Firm
      </div>
      <h1 class="page-title" style="margin-bottom: 12px;">Admin Security Gate Passed</h1>
      <p class="page-desc" style="max-width: 540px; margin: 0 auto 28px;">
        {{ message }} — You have successfully passed the backend <code>PermissionMiddleware</code> gate protecting <code>/admin-only</code>.
      </p>

      <div class="panel" style="text-align: left; margin-bottom: 28px; border-top: 3px solid var(--admin-1);">
        <div class="panel-header">
          <span class="panel-title">🛡 Permission Verification Details</span>
          <span class="badge badge-purple">HTTP 200 OK</span>
        </div>
        <div class="panel-body" style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Target Route:</span>
            <code style="background: rgba(255,255,255,0.06); padding: 2px 8px; border-radius: 4px; color: var(--admin-2);">GET /admin-only</code>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Middleware Guard:</span>
            <code>permission:admin.access</code>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Team Member Access:</span>
            <span class="badge badge-red">HTTP 403 Forbidden</span>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Client Access:</span>
            <span class="badge badge-red">HTTP 403 Forbidden</span>
          </div>
          <div class="flex items-center justify-between">
            <span style="color: var(--text-3);">Admin Access Status:</span>
            <span class="badge badge-green">Root Granted</span>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-center gap-12">
        <Link href="/dashboard" class="btn btn-primary">
          ← Back to Command Center
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
