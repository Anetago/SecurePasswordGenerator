# Simple Password Generator
[![Build Status](https://app.travis-ci.com/tk88e/PasswordGenerator.svg?branch=main)](https://app.travis-ci.com/tk88e/PasswordGenerator)
[![codecov](https://codecov.io/gh/tk88e/PasswordGenerator/branch/main/graph/badge.svg?token=8X531X31V9)](https://codecov.io/gh/tk88e/PasswordGenerator)

## Feature
- Supports CSPRNG algorithm (cryptographically secure pseudo random number generator)
- You choose the function which trims similar-looking characters.

## Prequisiteies
- PHP 7.4 and more (Prefer to the latest version)

## Installation

```
composer require gen-password/gen-password
```
or clone git repository

## How to use

### Lightweight
```php
<?php

$generator = new \SimplePasswordGenerator\PasswordGenerator();
print_r($generator->generate());
# -> ZPfKLae+.g{M
```

### Configuration
You will be able to set up to detailed settings.

#### ```setLength(int)```
You can be arbitrary length between 1 and 128 characters. By default, password length sets **12 character**.

#### ```useNumeric(bool)```
As you don't want to output a number like `012345678`, you turn 'false'.

#### ```useLowerAlphabet(bool)```
As you don't want to output lower alphabet like `abcdefghijklmnopqrstuvwxyz`, you turn `false`.

#### ```useUpperAlphabet(bool)```
As you don't want to output upper alphabet like `ABCDEFGHIJKLMNOPQRSTUVWXYZ`, you turn `false`.

#### ```useSymbols(bool)```
As you don't want to output symbols `!"#$%&\'()*+,-./:;<=>?@[\]^_``{\|}~`, you turn `false`.


Examples in detailed settings
```
<?php

$generator = new \SimplePasswordGenerator\PasswordGenerator();

$generator->setLength(18);
$generator->useNumeric(false);
$generator->useLowerAlphabet(true);
$generator->useUpperAlphabet(true);
$generator->useSymbols(false);
$generator->useTrimSimilarLooking(true);

print_r($generator->generate());
# ->YSmSXJvyNUtJKnddmc
```

## License
Apache License
