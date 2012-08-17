<?php

namespace exprlib\contexts;

class CosinScope extends namespace\Scope
{
    public function evaluate()
    {
        exit('@todo');
        return cos(deg2rad(parent::evaluate()));
    }
}
