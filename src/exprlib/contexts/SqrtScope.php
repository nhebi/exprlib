<?php

namespace exprlib\contexts;

class SqrtScope extends Scope
{
    public function evaluate()
    {
        return sqrt(parent::evaluate());
    }
}
