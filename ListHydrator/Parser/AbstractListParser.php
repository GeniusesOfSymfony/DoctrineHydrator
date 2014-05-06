<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser;

use Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper\ListMapperInterface;
use Gos\Component\DoctrineHydrator\ListHydrator\Parser\Walker\AbstractWalker;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategyInterface;

/**
 * Class AbstractListParser
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser
 * Iterator implementation to overload the loop behavior in your own parser
 * The iterator implementation able the process to parse many element in multi dimensional without make heavy process from child class.
 * In the most cases, we just need to parse a unique string, but we can imagine array instead.
 */
abstract class AbstractListParser implements ListParserInterface
{
    protected $position = 0;
    protected $element = array();
    protected $_mode;
    protected $_cloned = false;
    protected $_walkers;
    protected $_mapper;
    protected $_strategy;

    /**
     * @param $PK
     *
     * @return mixed
     */
    abstract public function parse($field);

    /**
     * @param $field
     */
    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * @param ListMapperInterface $mapper
     */
    public function setMapper(ListMapperInterface $mapper)
    {
        $this->_mapper = $mapper;
    }

    public function getMapper()
    {
        return $this->_mapper;
    }

    public function setStrategy(ListStrategyInterface $strategy)
    {
        $this->_strategy = $strategy;
        $this->getMapper()->setStrategy($this->_strategy);
    }

    /**
     * @param array $walkers
     *
     * @throws \Exception
     */
    public function registerWalker(array $walkers)
    {
        $this->_walkers = $walkers;
    }

    /**
     * @param $stack
     *
     * @return AbstractWalker
     * @throws \Exception
     */
    public function getWalker($stack)
    {
        $static = $this->getStaticWalker();
        $walker = $static::getInstance($stack, $this);

        if (!$walker instanceof AbstractWalker) {
            throw new \Exception(sprintf('Walker must be inherit of AbstractWalker, instance of %s given', get_class($walker)));
        }

        return $walker;
    }

    public function getStaticWalker()
    {
        return $this->_walkers[$this->_mode];
    }

    /**
     * @param $field
     * @param $mode
     *
     * @throws \Exception
     */
    public function assign($field, $mode)
    {
        $this->_mode = $mode;

        if (is_string($field) || is_array($field) || $field instanceof \Traversable) {

            if (method_exists($this, 'beforeParse')) {
                $field = $this->beforeParse($field);
            }

            $this->element = $this->parse($field);

            if (method_exists($this, 'afterParse')) {
                $this->afterParse();
            }
        } else {
            throw new \Exception(sprintf('The field given must be an array or implements Traversable interface, %s given', gettype($field)));
        }
    }

    public function isClone()
    {
        return $this->_cloned;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->element[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->element[$this->position]);
    }

    public function map(\Closure $p)
    {
        $this->element = array_map($p, $this->element);
    }

    public function walk(\Closure $p)
    {
        array_walk($this->element, $p);
    }

    public function filter(\Closure $p)
    {
        return array_filter($p, $this->element);
    }

    public function count()
    {
        return count($this->element);
    }

    public function toArray()
    {
        return $this->element;
    }

    public function first()
    {
        return current($this->element);
    }

    public function last()
    {
        return end($this->element);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->element);
    }

    public function offsetGet($offset)
    {
        $this->position = $offset;

        return $this->element[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->element[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->element[$offset]);
    }

    public function clear()
    {
        $this->element = array();
        $this->position = 0;
    }

    public function slice($offset, $length = null)
    {
        $this->element = array_slice($this->element, $offset, $length);
    }

    public function __clone()
    {
        $this->clear();
        $this->_cloned = true;
    }
}
