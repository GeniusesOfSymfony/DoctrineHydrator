<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser;

/**
 * Class ListParser
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser
 */
class ListParser extends AbstractListParser
{
    /**
     * Separator field
     */
    const PARSER_SEPARATOR = '.';

    /**
     * @param $field
     * @return array
     */
    public function parse($PK)
    {
        if ($this->_strategy->isTraversableSolution() && !$this->isClone()) {
            $buffer = array();

            foreach ($PK as $element) {
                $parser = clone $this;
                $parser->assign($element, $this->_mode);
                $buffer[] = $parser;
            }

            return $buffer;
        } else {
            return explode(self::PARSER_SEPARATOR, $PK); //That's all yeah, so hard.
        }
    }
}
