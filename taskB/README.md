# Task B: Simple WordPress Website

This directory contains the WordPress project setup and custom code.

## 1. Project Overview
- **Type**: Dockerized WordPress instance.
- **Theme**: Custom "ShoeStop" (Club Council) theme.
- **Plugin**: `SLC Connector` (Custom plugin to fetch GraphQL data).

## 2. Directory Structure
- `docker-compose.yml`: Defines the WordPress and MySQL containers.
- `wp-content/`:
  - `themes/shoestop`: The custom theme files (`header.php`, `footer.php`, `style.css`).
  - `plugins/slc-connector`: The plugin that provides `[slc_clubs]` and `[slc_events]` shortcodes.

## 3. How to Run
1.  **Prerequisite**: Ensure Task A containers are running (provides the GraphQL API at `http://localhost/graphql`).
2.  **Start WordPress**:
    ```bash
    cd taskB
    docker compose up -d
    ```
3.  **Setup**:
    - Go to `http://localhost:8080`.
    - Install WordPress (standard 2-minute install).
    - Go to **Appearance > Themes** and activate **ShoeStop Theme**.
    - Go to **Plugins** and activate **SLC Connector**.
    - Go to **Settings > Permalinks** and set to **Post name**.
4.  **Create Pages**:
    - Create a page named **Clubs**: Add content `[slc_clubs]`.
    - Create a page named **Events**: Add content `[slc_events]`.

## 4. Integration Details
- The WordPress container connects to the Task A Gateway via `host.docker.internal`.
- The plugin sends POST requests to `http://host.docker.internal/graphql` to fetch live data.
