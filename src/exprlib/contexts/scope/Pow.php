<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Pow extends Scope
{
    public function evaluate()
    {
        // log = log(x, $number (or 10))
        // ln = log(x)
        exit('ici');
        return cos(deg2rad(parent::evaluate()));
    }
}
