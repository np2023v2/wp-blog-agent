# Documentation Index

Welcome to the WP Blog Agent documentation! This index will help you find the information you need.

## Quick Links

- [README](../README.md) - Main plugin documentation
- [CHANGELOG](../CHANGELOG.md) - Version history and changes
- [CONTRIBUTING](../CONTRIBUTING.md) - How to contribute to the project
- [LICENSE](../LICENSE) - MIT License

## Getting Started

- [QUICKSTART.md](QUICKSTART.md) - Quick start guide for new users
- [Installation Instructions](../README.md#installation) - How to install and configure the plugin

## Development

### Architecture & Design
- [ARCHITECTURE.md](ARCHITECTURE.md) - Plugin architecture and design decisions
- [DEPLOYMENT.md](DEPLOYMENT.md) - Deployment guide and CI/CD workflows

### Implementation Guides
- [IMPLEMENTATION_SUMMARY.md](development/IMPLEMENTATION_SUMMARY.md) - Summary of implementation details
- [IMPLEMENTATION_BATCH_SERIES.md](development/IMPLEMENTATION_BATCH_SERIES.md) - Batch series generation implementation
- [IMPLEMENTATION_INLINE_IMAGES.md](development/IMPLEMENTATION_INLINE_IMAGES.md) - Inline images feature implementation
- [IMPROVEMENTS_SUMMARY.md](development/IMPROVEMENTS_SUMMARY.md) - Summary of improvements made
- [SETTINGS_OPTIMIZATION_SUMMARY.md](development/SETTINGS_OPTIMIZATION_SUMMARY.md) - Settings optimization details
- [TEXT_UTILS_USAGE.md](development/TEXT_UTILS_USAGE.md) - Text utilities documentation

### Testing
- [TEST_SUMMARY.md](development/TEST_SUMMARY.md) - General testing summary
- [QUEUE_TEST_SUMMARY.md](development/QUEUE_TEST_SUMMARY.md) - Queue system testing
- [SERIES_TESTING_GUIDE.md](development/SERIES_TESTING_GUIDE.md) - Series feature testing guide
- [SETTINGS_TEST_PLAN.md](development/SETTINGS_TEST_PLAN.md) - Settings testing plan
- [UI_PREVIEW.md](development/UI_PREVIEW.md) - UI preview and testing

## Features

### Core Features
- [HEALTH_CHECK_FEATURE.md](features/HEALTH_CHECK_FEATURE.md) - Health check system diagnostics
- [INLINE_IMAGES_FEATURE.md](features/INLINE_IMAGES_FEATURE.md) - AI-generated inline images
- [RANKMATH_SEO_FEATURE.md](features/RANKMATH_SEO_FEATURE.md) - RankMath SEO integration
- [BATCH_SERIES_GENERATION.md](features/BATCH_SERIES_GENERATION.md) - Batch series post generation
- [SERIES_FEATURE_SUMMARY.md](features/SERIES_FEATURE_SUMMARY.md) - Post series management
- [QUEUE_UI_OVERVIEW.md](features/QUEUE_UI_OVERVIEW.md) - Queue system and UI

## Plugin Structure

```
wp-blog-agent/
├── .github/workflows/       # CI/CD workflows
├── .wordpress-org/          # WordPress.org assets
├── admin/                   # Admin interface pages
├── assets/                  # CSS and JavaScript
├── docs/                    # Documentation (you are here)
│   ├── features/            # Feature-specific documentation
│   └── development/         # Development and testing docs
├── includes/                # Core plugin classes
├── wp-blog-agent.php        # Main plugin file
└── readme.txt               # WordPress.org readme
```

## API Documentation

### Main Classes

- **WP_Blog_Agent_Admin**: Admin interface and menu management
- **WP_Blog_Agent_Generator**: Content generation core logic
- **WP_Blog_Agent_Queue**: Task queue management
- **WP_Blog_Agent_Series**: Post series management
- **WP_Blog_Agent_OpenAI**: OpenAI API integration
- **WP_Blog_Agent_Gemini**: Google Gemini API integration
- **WP_Blog_Agent_Ollama**: Ollama API integration
- **WP_Blog_Agent_Image_Generator**: AI image generation
- **WP_Blog_Agent_RankMath**: RankMath SEO integration
- **WP_Blog_Agent_Health_Check**: System health diagnostics
- **WP_Blog_Agent_Logger**: Activity logging system

See [ARCHITECTURE.md](ARCHITECTURE.md) for detailed class documentation.

## Deployment & Release

- [DEPLOYMENT.md](DEPLOYMENT.md) - Complete deployment guide
  - WordPress.org deployment
  - GitHub releases
  - CI/CD workflows
  - Manual deployment fallback
  - Troubleshooting

## Support

- **GitHub Issues**: [https://github.com/np2023v2/wp-blog-agent/issues](https://github.com/np2023v2/wp-blog-agent/issues)
- **WordPress.org Support**: Coming soon after plugin approval

## Contributing

See [CONTRIBUTING.md](../CONTRIBUTING.md) for:
- Code of conduct
- Development setup
- Coding standards
- Pull request process
- Testing requirements

## License

This project is licensed under the MIT License - see [LICENSE](../LICENSE) for details.
