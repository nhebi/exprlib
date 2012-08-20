<?php

namespace exprlib\contexts;

class CosinScope extends Scope
{
    public function evaluate()
    {
        exit('@todo');

        return cos(deg2rad(parent::evaluate()));
    }
}
