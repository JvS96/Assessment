# GitHub Issue Tracker – Assessment Project

## Overview

This project is a small web application built to integrate with the GitHub REST API (v3) using OAuth authentication.

The application allows a user to:

- Authenticate via GitHub OAuth
- Retrieve and display all issues (open and closed) from a repository
- Parse and categorize issue labels (Client, Priority, Type)
- Create new issues via the GitHub API
- Automatically assign the logged-in GitHub user to newly created issues

This solution was implemented without using a full framework in order to demonstrate understanding of routing, request handling, service-layer architecture, and security principles.

---

## Tech Stack

- PHP 8.3
- GitHub REST API v3
- Vue.js (Frontend)
- PHPUnit (Testing)
- Docker (Optional)

---

## Architecture Overview

The application follows a layered structure:

app/  
&nbsp;&nbsp;Controllers/  
&nbsp;&nbsp;Services/  
&nbsp;&nbsp;Models/  
&nbsp;&nbsp;Core/  
&nbsp;&nbsp;Utils/

public/  
&nbsp;&nbsp;api/  
&nbsp;&nbsp;assets/  
&nbsp;&nbsp;bootstrap.php  
&nbsp;&nbsp;issue.php

### Controllers
Handle HTTP requests and orchestrate application flow.

### Services
Contain business logic and external integrations.

- `GitHubService` → Handles communication with GitHub API.
- `IssueService` → Handles issue transformation, label parsing, and assignment logic.

### Models
Represent domain entities such as `Issue`.

### Core
Contains infrastructure components:
- Request abstraction
- Response handling
- Session management
- CSRF protection

### Utils
Utility helpers such as `LabelParser` for extracting:
- Client (`C:` prefix)
- Priority (`P:` prefix)
- Type (`T:` prefix)

---

## OAuth Flow

1. User is redirected to GitHub authorization.
2. GitHub returns `code` and `state`.
3. State is validated to prevent CSRF.
4. Access token is requested from GitHub.
5. Access token and GitHub username are stored in session.
6. Token is used for authenticated API calls.

---

## Security Considerations

- OAuth state validation
- CSRF protection on issue creation
- Session regeneration after login
- Server-side validation
- Access token stored only in server session

---

## Installation

### 1. Clone Repository
git clone https://github.com/JvS96/Assessment.git

cd Assessment

### 2. Configure Environment Variables

Create a `.env` file:
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI=
GITHUB_REPO_OWNER=SwordfishCode
GITHUB_REPO_NAME=GitIntegration


### 3. Install Dependencies

composer install


### 4. Run Locally

Using PHP built-in server:
php -S localhost:8000 -t public

Visit:
http://localhost:8000


---

## Running Tests

vendor/bin/phpunit

Tests validate:
- API authentication handling
- Issue API responses
- Error handling behavior

---

## Design Decisions

- No framework was used to demonstrate architectural understanding.
- Service layer isolates GitHub API communication.
- Controllers remain thin and orchestration-focused.
- JSON endpoints are separated from view rendering.
- Vue is used for dynamic UI while keeping backend independent.

---

## Possible Improvements

If extended further:

- Implement central router (single entry point)
- Improve global exception handling
- Replace file_get_contents with dedicated HTTP client (e.g., Guzzle)
- Add more unit and integration tests
- Introduce caching for GitHub API responses
