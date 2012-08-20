exprlib - PHP
=============

[![Build Status](https://secure.travis-ci.org/rezzza/exprlib.png)](http://travis-ci.org/rezzza/exprlib)

An expression parser in PHP, code taken on [codehackit](http://codehackit.blogspot.fr/2011/08/expression-parser-in-php.html)

List of functionns:

- operators = * + -
- cos
- sin
- tan
- pow
- sqrt
- log
- exp
- sum
- avg

Examples:

```php
<?php
ParserModel::build('2+1')->evaluate(); // 3
ParserModel::build('2/1')->evaluate(); // 2
ParserModel::build('2/(3.6*8.5)')->evaluate(); // 0.06536
ParserModel::build('2+(6/2)+(8*3)')->evaluate(); // 29
ParserModel::build('2+3+6+6/2+3')->evaluate(); // 17
ParserModel::build('0.001 + 0.02')->evaluate(); // 0.021
ParserModel::build('COS(0)')->evaluate(); // 1
ParserModel::build('cos(90)')->evaluate(); // 0
ParserModel::build('cos(180)')->evaluate(); // -1
ParserModel::build('cos(360)')->evaluate(); // 1
ParserModel::build('sin(0)')->evaluate(); // 0
ParserModel::build('sin(90)')->evaluate(); // 1
ParserModel::build('sin(180)')->evaluate(); // 0
ParserModel::build('sqrt(9)')->evaluate(); // 3
ParserModel::build('sqrt(4)')->evaluate(); // 2
ParserModel::build('sqrt(3)')->evaluate(); // 1.73205
ParserModel::build('tan(180)')->evaluate(); // 0
ParserModel::build('log(10)')->evaluate(); // '1'
ParserModel::build('log(10,10)')->evaluate(); // '1'
ParserModel::build('ln(10)')->evaluate(); // '2.30259'
ParserModel::build('log(0.7)')->evaluate(); // '-0.1549'
ParserModel::build('ln(0.7)')->evaluate(); // '-0.35667'
ParserModel::build('pow(10, 2)')->evaluate(); // 100
ParserModel::build('pow(10, 3)')->evaluate(); // 1000
ParserModel::build('pow(10, 0)')->evaluate(); // 1
ParserModel::build('exp(12)')->evaluate(); // 162754.79142
ParserModel::build('exp(5.7)')->evaluate(); // 298.8674
ParserModel::build('sum(10, 20, 30)')->evaluate(); // 60
ParserModel::build('avg(10, 20, 30)')->evaluate(); // 20
```

# Launch tests

Look at .travis.yml

# Todo

+ Deal with +inf, -inf, and NaN
