| --------- | master | develop | feature |
| --------- | ------ | ------- | ------- |
| Build     | --- | --- | --- |
| Coverage  | --- | --- | --- |
| Analysis  | --- | --- | --- |
| json deps | --- | --- | --- |
| lock deps | --- | --- | --- |

# perf-ci-private

Latest deploy (inc. branches) https://perf-ci.herokuapp.com/en/

**Note: if you get an error, wait a minute & try again (refresh the page), the Application is probably deploying out or has gone to sleep.**

## Table of Contents

* [Contribution guidelines](/CONTRIBUTING.md)
* [Definition of Done](/doc/DefinitionOfDone.md)
* [Versioning successful builds - Release Candidates](/doc/Versioning.md)
* [Localisation](/doc/Localisation.md)
* [PageLayout](/doc/PageLayout.md)
* [Registration](/doc/Registration.md)

## Run app

1. `make dev.run`

Optionally add a **branch / release tag** `make dev.run branch=feature/story-123`

This will run the following:
* Switch to **branch / release** tag if requested
* Install any/all dependencies (composer install)
* Rebuild the database with fixtures
* Start WebServer

2. Then go to `http://localhost:8000`

## Run full test suite in parallel

```
make test.run
```

Output

```
...
30 scenarios (30 passed)
206 steps (206 passed)
0m13.12s (48.85Mb)
...
6 specs
16 examples (16 passed)
1132ms

Generating code coverage report in html format ...
Generating code coverage report in clover format ...
...
OK (4 tests, 3 assertions)
```


This Rebuild the database & will run the commands below.

### Behat command (using config in test/behat.yml)

```
make symfony.test.bdd
```

### PHPSpec

```
make symfony.test.spec
```

### PHPUnit

```
make symfony.test.unit
```

## Rebuild database

[Reference](https://github.com/eddiejaoude/dev-helper-cmds#database)

```
make symfony.dev.rebuild
```

---

These commands are only **wrappers**, you can still use the original commands if you wish.

More information on commands available visit [Make CMDs lib](https://github.com/eddiejaoude/dev-helper-cmds#built-in-commands)


---
---

## Contributions

Please read on [Contribution Guildlines](/CONTRIBUTING.md)
