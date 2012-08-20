<?php

namespace exprlib\contexts;

use exprlib\Parser;
use exprlib\exceptions\DivisionByZeroException;
use exprlib\exceptions\OutOfScopeException;
use exprlib\exceptions\UnknownTokenException;
use exprlib\contexts\scope;

class Scope implements IfContext
{
    protected $builder;
    protected $childrenContexts = array();
    protected $content;
    protected $operations = array();

    public function __construct($content = null)
    {
        $this->content = $content;
    }

    public function setBuilder(Parser $builder)
    {
        $this->builder = $builder;
    }

    public function addOperation($operation)
    {
        $this->operations[] = $operation;
    }

    /**
     * handle the next token from the tokenized list. example actions
     * on a token would be to add it to the current context expression list,
     * to push a new context on the the context stack, or pop a context off the
     * stack.
     */
    public function handleToken($token)
    {
        $baseToken = $token;
        $token     = strtolower($token);

        if (in_array($token, array('*','/','+','-','^'), true)) {
            $this->addOperation($token);
        } elseif ($token === ',') {
            $context = $this->builder->getContext();

            if (!$context instanceof ScopeGroup) {
                $group = new ScopeGroup();
                $group->setBuilder($this->builder);

                $this->builder->pushContext($group);
            } else {
                $group = $this;
            }

            $group->addScopeGroup($this->operations);
            $this->operations = array();
        } elseif ($token === '(') {
            $this->builder->pushContext(new Scope($token));
        } elseif ($token === ')') {

            $scopeOperation = $this->builder->popContext();
            $newContext     = $this->builder->getContext();
            if (is_null($scopeOperation) || (!$newContext)) {
                # this means there are more closing parentheses than openning
                throw new OutOfScopeException('It misses an open scope');
            }
            $newContext->addOperation($scopeOperation);

        } elseif ($token === 'sin(') {
            $this->builder->pushContext(new scope\Sin($token));
        } elseif ($token === 'cos(') {
            $this->builder->pushContext(new scope\Cosin($token));
        } elseif ($token === 'sum(') {
            $this->builder->pushContext(new scope\Sum($token));
        } elseif ($token === 'avg(') {
            $this->builder->pushContext(new scope\Avg($token));
        } elseif ($token === 'tan(') {
            $this->builder->pushContext(new scope\Tangent($token));
        } elseif ($token === 'sqrt(') {
            $this->builder->pushContext(new scope\Sqrt($token));
        } elseif ($token === 'log(' || $token === 'ln(') {
            $this->builder->pushContext(new scope\Log($token));
        } elseif ($token === 'pow(') {
            $this->builder->pushContext(new scope\Pow($token));
        } elseif ($token === 'exp(') {
            $this->builder->pushContext(new scope\Exp($token));
        } else {
            if (is_numeric($token)) {
                $this->addOperation((float) $token);
            } else {
                throw new UnknownTokenException(sprintf('"%s" is not supported yet', $baseToken));
            }
        }
    }

    /**
     * order of operations:
     * - parentheses, these should all ready be executed before this method is called
     * - exponents, first order
     * - mult/divi, second order
     * - addi/subt, third order
     */
    protected function expressionLoop()
    {
        while (list($i, $operation) = each ($this->operations)) {
            if (!in_array($operation, array('^','*','/','+','-'), true)) {
                continue;
            }

            $left =  isset($this->operations[$i - 1]) ? (float) $this->operations[$i - 1] : null;
            $right = isset($this->operations[$i + 1]) ? (float) $this->operations[$i + 1] : null;

            $firstOrder = (in_array('^', $this->operations, true));
            $secondOrder = (in_array('*', $this->operations, true) || in_array('/', $this->operations, true));
            $thirdOrder = (in_array('-', $this->operations, true) || in_array('+', $this->operations, true));

            $removeSides = true;
            if ($firstOrder) {
                switch ($operation) {
                    case '^':
                        $this->operations[$i] = pow((float) $left, (float) $right);
                        break;
                    default:
                        $removeSides = false;
                        break;
                }
            } elseif ($secondOrder) {
                switch ($operation) {
                    case '*':
                        $this->operations[$i] = (float) ($left * $right);
                        break;
                    case '/':

                        if ($right == 0) {
                            throw new DivisionByZeroException();
                        }

                        $this->operations[$i] = (float) ($left / $right);
                        break;
                    default:
                        $removeSides = false;
                        break;
                }
            } elseif ($thirdOrder) {
                switch ($operation) {
                    case '+':
                        $this->operations[$i] = (float) ($left + $right);
                        break;
                    case '-':
                        $this->operations[$i] = (float) ($left - $right);
                        break;
                    default:
                        $removeSides = false;
                        break;
                }
            }

            if ($removeSides) {
                unset($this->operations[$i+1], $this->operations[$i-1]);
                $this->operations = array_values($this->operations);
                reset($this->operations);
            }
        }

        if (count($this->operations) === 1) {
            return end($this->operations);
        }

        return false;
    }

    # order of operations:
    # - sub scopes first
    # - multiplication, division
    # - addition, subtraction
    # evaluating all the sub scopes (recursivly):
    public function evaluate()
    {
        foreach ($this->operations as $i => $operation) {
            if (is_object($operation)) {
                $this->operations[$i] = $operation->evaluate();
            }
        }
        $operationList = $this->operations;

        while (true) {
            $operationCheck = $operationList;
            $result = $this->expressionLoop();

            if ($result !== false) {
                return $result;
            }

            if ($operationCheck === $operationList) {
                break;
            } else {
                reset($operationList = array_values($operationList));
            }
        }
    }
}
