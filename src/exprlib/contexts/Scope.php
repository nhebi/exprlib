<?php

namespace exprlib\contexts;

use exprlib\Parser;
use exprlib\exceptions\OutOfScopeException;
use exprlib\exceptions\UnknownTokenException;

class Scope implements IfContext
{
    protected $builder = null;
    protected $childrenContexts = array();
    protected $rawContent = array();
    protected $operations = array();

    const T_NUMBER = 1;
    const T_OPERATOR = 2;
    const T_SCOPE_OPEN = 3;
    const T_SCOPE_CLOSE = 4;
    const T_SIN_SCOPE_OPEN = 5;
    const T_COS_SCOPE_OPEN = 6;
    const T_TAN_SCOPE_OPEN = 7;
    const T_SQRT_SCOPE_OPEN = 8;

    public function setBuilder(Parser $builder)
    {
        $this->builder = $builder;
    }

    public function __toString()
    {
        return implode('', $this->rawContent);
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
        $type = null;

        if (in_array( $token, array('*','/','+','-','^') )) {
            $type = self::T_OPERATOR;
        } elseif ($token === ')') {
            $type = self::T_SCOPE_CLOSE;
        } elseif ($token === '(' ) {
            $type = self::T_SCOPE_OPEN;
        } elseif ($token === 'sin(' ) {
            $type = self::T_SIN_SCOPE_OPEN;
        } elseif ($token === 'cos(' ) {
            $type = self::T_COS_SCOPE_OPEN;
        } elseif ($token === 'tan(' ) {
            $type = self::T_TAN_SCOPE_OPEN;
        } elseif ($token === 'sqrt(' ) {
            $type = self::T_SQRT_SCOPE_OPEN;
        }

        if (null === $type) {
            if (is_numeric($token)) {
                $type = self::T_NUMBER;
                $token = (float) $token;
            }
        }

        switch ($type) {
            case self::T_NUMBER:
            case self::T_OPERATOR:
                $this->operations[] = $token;
                break;
            case self::T_SCOPE_OPEN:
                $this->builder->pushContext(new Scope());
                break;
            case self::T_SIN_SCOPE_OPEN:
                $this->builder->pushContext(new SinScope());
                break;
            case self::T_COS_SCOPE_OPEN:
                $this->builder->pushContext(new CosinScope());
                break;
            case self::T_TAN_SCOPE_OPEN:
                $this->builder->pushContext(new TangentScope());
                break;
            case self::T_SQRT_SCOPE_OPEN:
                $this->builder->pushContext(new SqrtScope());
                break;
            case self::T_SCOPE_CLOSE:
                $scopeOperation = $this->builder->popContext();
                $newContext = $this->builder->getContext();
                if (is_null($scopeOperation) || (!$newContext)) {
                    # this means there are more closing parentheses than openning
                    throw new OutOfScopeException();
                }
                $newContext->addOperation($scopeOperation);
                break;
            default:
                throw new UnknownTokenException($token);
                break;
        }
    }

    /**
     * order of operations:
     * - parentheses, these should all ready be executed before this method is called
     * - exponents, first order
     * - mult/divi, second order
     * - addi/subt, third order
     */
    protected function expressionLoop(&$operationList)
    {
        while (list($i, $operation) = each ($operationList)) {
            if (!in_array($operation, array('^','*','/','+','-'))) {
                continue;
            }

            $left =  isset($operationList[$i - 1]) ? (float) $operationList[$i - 1] : null;
            $right = isset($operationList[$i + 1]) ? (float) $operationList[$i + 1] : null;

            $firstOrder = (in_array('^', $operationList));
            $secondOrder = (in_array('*', $operationList) || in_array('/', $operationList));
            $thirdOrder = (in_array('-', $operationList) || in_array('+', $operationList));

            $removeSides = true;
            if ($firstOrder) {
                switch ($operation) {
                    case '^':
                        $operationList[$i] = pow((float) $left, (float) $right);
                        break;
                    default:
                        $removeSides = false;
                        break;
                }
            } elseif ($secondOrder) {
                switch ($operation) {
                    case '*':
                        $operationList[ $i ] = (float) ($left * $right);
                        break;
                    case '/':
                        $operationList[ $i ] = (float) ($left / $right);
                        break;
                    default:
                        $removeSides = false;
                        break;
                }
            } elseif ($thirdOrder) {
                switch ($operation) {
                    case '+':
                        $operationList[ $i ] = (float) ($left + $right);
                        break;
                    case '-':
                        $operationList[ $i ] = (float) ($left - $right);
                        break;
                    default:
                        $removeSides = false;
                        break;
                }
            }

            if ($removeSides) {
                unset($operationList[$i+1], $operationList[$i-1]);
                reset($operationList = array_values($operationList));
            }
        }

        if (count($operationList) === 1) {
            return end($operationList);
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
            $result = $this->expressionLoop($operationList);

            if ($result !== false) {
                return $result;
            }

            if ($operationCheck === $operationList) {
                break;
            } else {
                reset($operationList = array_values($operationList));
            }
        }
        throw new \Exception('failed... here');
    }
}
