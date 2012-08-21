exprlib - PHP
=============

[![Build Status](https://secure.travis-ci.org/rezzza/exprlib.png)](http://travis-ci.org/rezzza/exprlib)

An expression parser in PHP, code inspired from [codehackit](http://codehackit.blogspot.fr/2011/08/expression-parser-in-php.html)

List of functions:

- operators = * + -
- acos
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
exprlib\Parser::build('2+1')->evaluate(); // 3
exprlib\Parser::build('2/1')->evaluate(); // 2
exprlib\Parser::build('2/(3.6*8.5)')->evaluate(); // 0.06536
exprlib\Parser::build('2+(6/2)+(8*3)')->evaluate(); // 29
exprlib\Parser::build('2+3+6+6/2+3')->evaluate(); // 17
exprlib\Parser::build('0.001 + 0.02')->evaluate(); // 0.021
exprlib\Parser::build('COS(0)')->evaluate(); // 1
exprlib\Parser::build('cos(90)')->evaluate(); // 0
exprlib\Parser::build('cos(180)')->evaluate(); // -1
exprlib\Parser::build('cos(360)')->evaluate(); // 1
exprlib\Parser::build('sin(0)')->evaluate(); // 0
exprlib\Parser::build('sin(90)')->evaluate(); // 1
exprlib\Parser::build('sin(180)')->evaluate(); // 0
exprlib\Parser::build('sqrt(9)')->evaluate(); // 3
exprlib\Parser::build('sqrt(4)')->evaluate(); // 2
exprlib\Parser::build('sqrt(3)')->evaluate(); // 1.73205
exprlib\Parser::build('tan(180)')->evaluate(); // 0
exprlib\Parser::build('log(10)')->evaluate(); // '1'
exprlib\Parser::build('log(10,10)')->evaluate(); // '1'
exprlib\Parser::build('ln(10)')->evaluate(); // '2.30259'
exprlib\Parser::build('log(0.7)')->evaluate(); // '-0.1549'
exprlib\Parser::build('ln(0.7)')->evaluate(); // '-0.35667'
exprlib\Parser::build('pow(10, 2)')->evaluate(); // 100
exprlib\Parser::build('pow(10, 3)')->evaluate(); // 1000
exprlib\Parser::build('pow(10, 0)')->evaluate(); // 1
exprlib\Parser::build('exp(12)')->evaluate(); // 162754.79142
exprlib\Parser::build('exp(5.7)')->evaluate(); // 298.8674
exprlib\Parser::build('sum(10, 20, 30)')->evaluate(); // 60
exprlib\Parser::build('avg(10, 20, 30)')->evaluate(); // 20
exprlib\Parser::build('log(0)')->evaluate(); // -INF
exprlib\Parser::build('log(0)*-1')->evaluate(); // INF
exprlib\Parser::build(sprintf('acos(%s)', rad2deg(8))->evaluate(); // NAN
```

# Launch tests

Look at .travis.yml

# Todo

+ Look at how is the best way to decouple Scope
+ Add tests
