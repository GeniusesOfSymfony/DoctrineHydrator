<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Strategy;

/**
 * Class AbstractListStrategy
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Strategy
 */
abstract class AbstractListStrategy implements ListStrategyInterface
{
    /**
     * @var null
     */
    public static $_strategy = null;

    /**
     * @var null
     */
    protected $_solution = null;

    /**
     * @var null
     * In case of failure, we retrieve the default strategy based on the predicate from the schema
     */
    protected static $_fallback = null;

    /**
     * @return null
     */
    final public function getSolution()
    {
        if (is_scalar($this->_solution) || null === $this->_solution) {
            return $this->_solution;
        }
    }

    /**
     * Prevent cloning
     */
    private function __clone() { }
}
