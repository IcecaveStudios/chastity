# Chastity

[![Build Status]](https://travis-ci.org/IcecaveStudios/chastity)
[![Test Coverage]](https://coveralls.io/r/IcecaveStudios/chastity?branch=develop)
[![SemVer]](http://semver.org)

**Chastity** provides an abstraction for acquiring and releasing advisory locks
in a distributed environment.

* Install via [Composer](http://getcomposer.org) package [icecave/chastity](https://packagist.org/packages/icecave/chastity)
* Read the [API documentation](http://icecavestudios.github.io/chastity/artifacts/documentation/api/)

## Drivers

* [Redis](src/Driver/Redis)
* Redlock (not yet implemented)
* PDO (not yet implemented)
* MySQL advisory locks (not yet implemented)

## Examples

* [Acquiring a lock](examples/acquire)
* [Extending an already acquired lock](examples/acquire)

## Contact us

* Follow [@IcecaveStudios](https://twitter.com/IcecaveStudios) on Twitter
* Visit the [Icecave Studios website](http://icecave.com.au)
* Join `#icecave` on [irc.freenode.net](http://webchat.freenode.net?channels=icecave)

<!-- references -->
[Build Status]: http://img.shields.io/travis/IcecaveStudios/chastity/develop.svg?style=flat-square
[Test Coverage]: http://img.shields.io/coveralls/IcecaveStudios/chastity/develop.svg?style=flat-square
[SemVer]: http://img.shields.io/:semver-0.0.0-red.svg?style=flat-square
