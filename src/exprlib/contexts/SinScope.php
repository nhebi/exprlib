<?php

namespace exprlib\contexts;

class SinScope extends namespace\Scope
{
    public function evaluate()
    {
        return sin(deg2rad(parent::evaluate()));
    }
}
