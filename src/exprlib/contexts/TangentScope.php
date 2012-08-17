<?php

namespace exprlib\contexts;

class TangentScope extends namespace\Scope
{
    public function evaluate()
    {
        exit('@todo');
        return cos(deg2rad(parent::evaluate()));
    }
}
