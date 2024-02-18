# Contributing to Golden

Thank you for considering contributing to Golden! We appreciate your time and effort in making this project better.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
  - [Reporting Bugs](#reporting-bugs)
  - [Suggesting Enhancements](#suggesting-enhancements)
  - [Code Contribution](#code-contribution)
- [Development Setup](#development-setup)
- [Pull Request Process](#pull-request-process)
- [Code Style](#code-style)
- [Testing](#testing)
- [Documentation](#documentation)
- [Community](#community)
- [License](#license)

## Code of Conduct

Please note that this project adheres to the [Contributor Covenant Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to [thetalkginbit@gmail.com](mailto:thetalkginbit@gmail.com).

## How Can I Contribute?

### Reporting Bugs

If you encounter a bug, please open an issue on the [GitHub issue tracker](https://github.com/franiglesias/php-golden/issues) with a detailed description of the problem, steps to reproduce, and any relevant information.

### Suggesting Enhancements

If you have ideas for enhancements, new features, or improvements, we welcome your suggestions. Please [open an issue](https://github.com/franiglesias/php-golden/issues) to discuss your ideas and gather feedback from the community.

### Documentation

Feel free to improve documentation, add how-tos or recipes. Also, feel free to write blog entries describing Golden, record screencasts or whatever contribution that can help others to use Golden in their own projects.

### Code Contribution

If you want to contribute code to Golden, follow these steps:

1. Fork the repository on GitHub.
2. Clone your forked repository to your local machine.
3. Create a new branch for your changes: `git checkout -b feature/your-feature`.
4. Make your changes and ensure that the code follows the [code style](#code-style).
5. Write tests to cover your changes.
6. Ensure all tests pass: `go test ./...`.
7. Update the documentation if necessary.
8. Push your changes to your forked repository.
9. Create a pull request against the `main` branch of the main repository.

### Most interesting code contributions

* **Normalizers**: that convert posible outputs to be persisted as snapshots.
* **Scrubbers**: to manage non-deterministic outputs.
* **Reporters**: to show the differences between the snapshot and the current output.

## Development Setup

To set up your development environment, follow these steps:

1. Clone the repository: `git clone https://github.com/franiglesias/golden.git`.
2. Change to the project directory: `cd golden`.
3. Install dependencies: `go get -u ./...`.

## Pull Request Process

1. Ensure your pull request addresses a specific issue or is well-described.
2. Follow the [code style guidelines](#code-style).
3. Include relevant tests for your changes.
4. Ensure all tests pass before submitting the pull request.
5. Update the documentation if needed.
6. Squash your commits into a single commit.

## Code Style

As a general rule, follow the recommendations of [FIG Standards](https://github.com/php-fig/fig-standards)

## Testing

Make sure to run tests before submitting a pull request:

```bash
bin/phpunit
```

Also, make sure that all new code is covered with tests.

## Documentation

Keep the project documentation up-to-date. If you make changes that require documentation updates, please include those changes in your pull request.

## Community

Join [Project Discussions](https://github.com/franiglesias/php-golden/discussions) to discuss development, ask questions, or share your experiences.

## License

By contributing to Golden, you agree that your contributions will be licensed under the [MIT License](LICENSE).

Happy coding!
