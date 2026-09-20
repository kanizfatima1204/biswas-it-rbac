# Biswas IT Firm — Role-Based Dashboard Architecture

Laravel 12 + Vue 3 + Inertia.js prototype for Client, Admin and Team Member dashboard access.

## 1. System Architecture

Browser → Inertia/Vue 3 → Laravel Web Routes → Auth Session → Role/Permission Middleware → Controller → Inertia Page.

- Authentication: Laravel session authentication.
- Authorization: `users.role` + `permissions` + `role_permissions`.
- Route protection: `auth`, `role:*`, and `permission:*` middleware.
- UI receives only the authenticated user's role/permission list through the Inertia shared props.
- Server-side middleware remains the source of truth; hiding UI elements is not security.

## 2. User Roles

| Role | Purpose |
|---|---|
| Admin | Agency-wide administration |
| Team Member | Assigned work and project execution |
| Client | Own service/project portal |

## 3. Permission Matrix

| Permission | Admin | Team Member | Client |
|---|:---:|:---:|:---:|
| admin.access | ✓ | — | — |
| users.view | ✓ | — | — |
| projects.manage | ✓ | — | — |
| tasks.manage | ✓ | ✓ | — |
| tasks.view | ✓ | ✓ | — |
| projects.view | ✓ | ✓ | ✓ |
| requests.create | ✓ | — | ✓ |

## 4. Database Structure

- `users`: identity + `role`.
- `permissions`: permission catalog.
- `role_permissions`: role-to-permission mapping.
- Laravel's default session/cache/job tables can be added by standard framework migrations when needed.

## 5. Working Prototype

Demo accounts:
- admin@biswasit.test / password
- team@biswasit.test / password
- client@biswasit.test / password

The dashboard component is selected from the authenticated role. Protected test endpoints are included:
- `/admin-only` → requires `admin.access`.
- `/team-only` → requires `admin` or `team_member` role.

## 6. GitHub Repository

This package is Git-ready. Create an empty GitHub repository, then run the commands below.

## 7. Security Considerations

- Passwords are hashed using Laravel's `hashed` cast.
- Session ID is regenerated after login.
- Logout invalidates the session and regenerates the CSRF token.
- Login is rate-limited with Laravel's `throttle:login` middleware.
- Authorization is enforced server-side through middleware.
- Production should use HTTPS, secure cookies, environment secrets, least-privilege database credentials, audit logs, 2FA for admins, and explicit policies for resource ownership.

## 8. Future Development Plan

1. Add Projects, Tasks, Clients and Teams modules.
2. Replace dashboard demo stats with real database queries.
3. Add Laravel Policies for row-level/resource authorization.
4. Add audit logging and admin activity history.
5. Add 2FA, password reset and email verification.
6. Add organization/tenant isolation if multiple agencies are supported.
7. Add automated feature/security tests and CI/CD.
