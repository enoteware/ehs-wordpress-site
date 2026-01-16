# EHS Analytical Solutions

WordPress-based website for EHS Analytical Solutions (ehsanalytical.com), a California-based environmental health and safety consulting firm.

## Quick Start

1. **Read the documentation:** [docs/index.md](docs/index.md)
2. **Set up development environment:** [docs/dev-setup.md](docs/dev-setup.md)
3. **Review architecture:** [docs/architecture.md](docs/architecture.md)
4. **Check workflows:** [docs/workflows.md](docs/workflows.md)

## For AI Coding Agents

- **Start here:** [docs/index.md](docs/index.md)
- **Development guide:** [CLAUDE.md](CLAUDE.md)
- **Cursor rules:** [.cursorrules](.cursorrules)

## Project Structure

```
ehs/
├── docs/                    # Documentation knowledge base
├── ehs-wordpress-local/     # Local WordPress development (DDEV)
├── migration-scripts/        # Server migration automation
├── project-organization/    # Content and implementation guides
└── *.js                     # Task management scripts
```

## Key Features

- **WordPress Site** - Elementor Pro-based website
- **Task Management** - Node.js automation scripts
- **Template Sync** - Elementor template synchronization
- **Migration Planning** - Server migration tools

## Development

### Local Setup

```bash
cd ehs-wordpress-local
ddev start
```

Access: http://ehs-mini.ddev.site

### Common Commands

```bash
# Regenerate Elementor CSS
cd ehs-wordpress-local && ./regen-css.sh

# Sync templates from production
cd ehs-wordpress-local && ./sync-elementor-templates.sh

# Task management
node create-task-checklists.js
```

## Documentation

- **[Architecture](docs/architecture.md)** - System overview and components
- **[Development Setup](docs/dev-setup.md)** - Installation and configuration
- **[Workflows](docs/workflows.md)** - Common development tasks
- **[Runbooks](docs/runbooks.md)** - Debugging and troubleshooting

## License

Proprietary - EHS Analytical Solutions, Inc.
