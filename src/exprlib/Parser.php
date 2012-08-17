<?php

namespace exprlib;

/**
 * this model handles the tokenizing, the context stack functions, and
 * the parsing (token list to tree trans).
 * as well as an evaluate method which delegates to the global scopes evaluate.
 */
class Parser
{
    protected $_content = null;
    protected $_context_stack = array();
    protected $_tree = null;
    protected $_tokens = array();

    public function __construct($content = null)
    {
        if ($content) {
            $this->set_content($content);
        }
    }

    /**
     * this function does some simple syntax cleaning:
     * - removes all spaces
     * - replaces '**' by '^'
     * then it runs a regex to split the contents into tokens. the set
     * of possible tokens in this case is predefined to numbers (ints of floats)
     * math operators (*, -, +, /, **, ^) and parentheses.
     */
    public function tokenize()
    {
        $this->_content = str_replace(array("\n","\r","\t"," "), '', $this->_content);
        $this->_content = str_replace('**', '^', $this->_content);
        $this->_content = str_replace('PI', (string) PI(), $this->_content);
        $this->_tokens = preg_split(
            '@([\d\.]+)|(sin\(|cos\(|tan\(|sqrt\(|\+|\-|\*|/|\^|\(|\))@',
            $this->_content,
            null,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        return $this;
    }

    /**
     * this is the the loop that transforms the tokens array into
     * a tree structure.
     */
    public function parse()
    {
        # this is the global scope which will contain the entire tree
        $this->push_context(new \exprlib\contexts\Scope());
        foreach ($this->_tokens as $token) {
            # get the last context model from the context stack,
            # and have it handle the next token
            $this->get_context()->handle_token($token);
        }
        $this->_tree = $this->pop_context();

        return $this;
    }

    public function evaluate()
    {
        if (!$this->_tree) {
            throw new \exprlib\exceptions\ParseTreeNotFoundException();
        }

        return $this->_tree->evaluate();
    }

    /*** accessors and mutators ***/

    public function get_tree()
    {
        return $this->_tree;
    }

    public function set_content($content = null)
    {
        $this->_content = $content;

        return $this;
    }

    public function get_tokens()
    {
        return $this->_tokens;
    }

    /*******************************************************
     * the context stack functions. for the stack im using
     * an array with the functions array_push, array_pop,
     * and end to push, pop, and get the current element
     * from the stack.
     *******************************************************/

    public function push_context(\exprlib\contexts\IfContext $context)
    {
        array_push( $this->_context_stack, $context );
        $this->get_context()->set_builder( $this );
    }

    public function pop_context()
    {
        return array_pop($this->_context_stack);
    }

    public function get_context()
    {
        return end($this->_context_stack);
    }
}
