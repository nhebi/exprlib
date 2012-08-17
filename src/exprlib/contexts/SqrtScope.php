<?php

namespace exprlib\contexts;

class SqrtScope extends namespace\Scope
{
    public function evaluate()
    {
        return sqrt(parent::evaluate());
    }
}
