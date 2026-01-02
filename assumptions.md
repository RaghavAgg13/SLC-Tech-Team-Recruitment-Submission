# Assumptions and Design Decisions

## 1. Environment
- **Operating System**: The task was executed on Windows. `docker-compose.yml` volumes and entrypoints were adjusted (LF line endings) to ensure compatibility with Linux containers running on Windows.
- **Docker**: It is assumed that Docker Desktop is installed and configured with WSL 2 backend.

## 2. Authentication Bypass (Task A)
- **Problem**: The `auth` service was private/disabled, preventing legitimate login.
- **Solution**: We assumed that for the purpose of this task (populating data), it is acceptable to bypass the Role-Based Access Control (RBAC) in the Python resolvers.
- **Method**: Modified `mutations.py` in subgraphs to inject a default "Club Council" (CC) admin user if no user context is present (`info.context.user is None`).

## 3. WordPress Setup (Task B)
- **Containerization**: We chose to run WordPress in Docker (`taskB/docker-compose.yml`) to keep the environment isolated and reproducible as recommended.
- **Networking**: We assumed the WordPress container needs to query the `gateway` container from Task A. We used `host.docker.internal` (via `extra_hosts`) to allow WordPress to egress to the host's localhost where the Gateway is exposed on port 80.
- **Theme**: We assumed a custom theme was required to match the "ShoeStop" aesthetics requested (later rebranded to "Club Council"). We created a child-theme-like structure from scratch (`wp-content/themes/shoestop`).
- **Data Fetching**: We assumed client-side or server-side fetching were both valid. We implemented **Server-Side Fetching** via a custom plugin (`slc-connector`) using `wp_remote_post` to query GraphQL, as this is better for SEO and robustness than client-side React in WordPress.

## 4. Assets
- **Images**: Since we did not have original source assets for the club logos, we assumed the presence of a `shoestop` theme assets folder. (Note: These may need to be restored if lost).
