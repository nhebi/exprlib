<?php

namespace exprlib\contexts;

class SinScope extends Scope
{
    public function evaluate()
    {
        return sin(deg2rad(parent::evaluate()));
    }
}
