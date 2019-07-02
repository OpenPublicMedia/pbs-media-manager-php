# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on _TK_.

All code should adhere to the [PSR-2 Coding Standard](https://www.php-fig.org/psr/psr-2/)

## Pull Requests

- **Add tests!** - New/significantly refactored functionality must have tests.

- **Document any change in behaviour** - Make sure the README and any other 
relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow SemVer. Randomly breaking 
public APIs is not an option.

- **Create topic branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send
multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull
request is meaningful. If you had to make multiple intermediate commits while
developing, please squash them before submitting.

- **Ensure tests pass!** - Please run the tests (see below) before submitting
your pull request, and make sure they pass. We won't accept a patch until all
tests pass.

- **Ensure no coding standards violations** - Please run PHP Code Sniffer using
the PSR-2 standard (see below) before submitting your pull request.

## Running Tests

``` bash
$ ./vendor/bin/phpunit
```

## Running PHP Code Sniffer

``` bash
$ ./vendor/bin/phpcs src --standard=psr2 -sp
```

**Happy coding**!
