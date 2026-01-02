# Task A: Run Codebase Locally in Docker

This directory contains the details and configuration used to run the SLC Recruitment codebase locally.

## Project Structure
The project consists of several microservices orchestrated via Docker Compose:
- **Web**: Next.js frontend.
- **Gateway**: Apollo Federation gateway.
- **Subgraphs**: `clubs`, `members`, `events`, `users`, `interfaces`.
- **Mongo**: Database.

## Setup Steps Executed
1.  **Cloning**: Verified the repository and initialized submodules via HTTPS.
2.  **Configuration**:
    - Created `.env` files for all services using the provided examples.
    - **Modification**: Analyzed `docker-compose.yml` and identified missing/private services (`auth`, `files`) that were preventing startup.
3.  **Fixes Applied**:
    - Commented out `auth` and `files` services in `docker-compose.yml`.
    - Updated `nginx/default.conf` (if applicable) to remove routing to these disabled services.
    - Updated entrypoint scripts to use LF line endings (fixing Windows CRLF issues).
4.  **Running**:
    - Executed `docker compose up -d --build`.
    - Verified all containers are healthy.

## Files
- `docker-compose.yml`: The modified configuration file used to run the stack.
- `populate_data.py`: Script to populate the database with dummy data.

---

# Part 2: GraphQL Operations & Auth Bypass

This section details the steps taken to bypass authentication and populate the database with mock data.

## 1. Authentication Bypass
Since the `auth` service was disabled, we needed a way to perform privileged mutations (creating clubs, adding members).
- **Strategy**: Role-based access control (RBAC) in the resolvers checks `info.context.user`.
- **Implementation**: We modified the `mutations.py` files in `subgraphs/members`, `subgraphs/clubs`, and `subgraphs/events`.
- **Code Change**:
  ```python
  # Injected Mock User
  user = info.context.user
  if user is None:
      user = {"role": "cc", "uid": "cc"}
  ```
  This treats every unauthenticated request as coming from the "Clubs Council" (CC) admin, allowing full access.

## 2. Important Points To Note
1.  The GraphQL schema expected `startYear` and `endYear` (camelCase),
2.  The `getUserNameFromUID` utility assumed all UIDs followed `firstname.lastname` format.

## 3. Data Population
A Python script (`populate_data.py`) was created to automate the process:
1.  **Clubs**: Creates 3 clubs (Coding, Music, Dance).
2.  **Members**: Adds 3 members per club (1 POC, 2 regular).
3.  **Events**: Creates 2 events per club.

---

## 4. Screenshots

### Localhost Running
![Localhost running with Docker](../images/localhost.png)

### All Clubs View
![All clubs displayed](../images/all%20clubs.png)

### Club Details
![Club details page - view 1](../images/club1%20%231.png)

![Club details page - view 2](../images/club1%20%232.png)

### All Events View
![All events displayed](../images/all%20events.png)
