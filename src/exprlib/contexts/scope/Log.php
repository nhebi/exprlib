<?php

namespace exprlib\contexts\scope;

use exprlib\contexts\Scope;

class Log extends Scope
{
    public function evaluate()
    {
        $content = (string) $this->content;

        if ($content == 'log(') {
            return log10(parent::evaluate());
        } else { // ln
            return log(parent::evaluate());
        }
    }
}
