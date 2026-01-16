# EHS Analytical Solutions - Documentation

Welcome to the EHS Analytical Solutions project documentation. This knowledge base is organized for both human developers and AI coding agents.

## Quick Links

- **[Architecture Overview](architecture.md)** - System architecture, main modules, data flow
- **[Development Setup](dev-setup.md)** - Installation, environment variables, required tools
- **[Hosting Infrastructure](hosting.md)** - DigitalOcean, Nexcess, DDEV server details
- **[Workflows](workflows.md)** - Common development tasks, build/test/lint/release processes
- **[Runbooks](runbooks.md)** - Debugging playbooks, log locations, known issues

## Project Overview

This is a WordPress-based project for EHS Analytical Solutions (ehsanalytical.com), a California-based environmental health and safety consulting firm. The repository manages:

- **WordPress Site** - Main website built with Elementor Pro
- **Task Management** - Node.js scripts for project management automation
- **Template Sync** - Elementor template synchronization between environments
- **Migration Planning** - Server migration from Nexcess to DigitalOcean

## Key Directories

- `ehs-wordpress-local/` - Local WordPress development (DDEV)
- `migration-scripts/` - Server migration automation
- `project-organization/` - Content and implementation guides
- Root level - Task management scripts (Node.js)

## Getting Started

1. Read [Development Setup](dev-setup.md) for environment configuration
2. Review [Architecture Overview](architecture.md) to understand the system
3. Check [Workflows](workflows.md) for common tasks
4. See [CLAUDE.md](../CLAUDE.md) for detailed development guidance

## For AI Agents

- **Entry Point**: Start with this file, then follow links based on task
- **Code Changes**: Review architecture.md first to understand impact
- **Debugging**: Check runbooks.md for known issues and solutions
- **Setup Questions**: Refer to dev-setup.md for environment requirements
