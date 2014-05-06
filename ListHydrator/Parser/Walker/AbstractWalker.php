<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser\Walker;

use Doctrine\Common\Collections\ArrayCollection;
use Gos\Component\DoctrineHydrator\ListHydrator\Parser\ListParserInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractWalker
{
    /**
     * @var Singleton instance
     */
    public static $_instance = null;

    /**
     * @var null
     * Able to retro-active behavior
     * Represent the way to access at the data
     * Keep the logic of a walker (texas ranger)
     */
    protected $_traveled = null;

    /**
     * @var $_stack
     */
    protected $_stack = null;

    /**
     * @var \Gos\Component\DoctrineHydrator\ListHydrator\Parser\ListParserInterface
     */
    protected $_parser;

    /**
     * @var Property accessor
     */
    protected $_accessor;

    abstract protected function aggregate($entry, $uow);

    abstract protected function isVertical($walked);

    abstract protected function isHorizontal($walked);

    abstract protected function walkVertically($walked);

    abstract protected function walkHorizontally($walked);

    protected function __construct($stack, ListParserInterface $parser)
    {
        $this->_stack = $stack;
        $this->_traveled = new ArrayCollection();
        $this->_parser = $parser;
        $this->_accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return null
     */
    final public static function getInstance($stack, ListParserInterface $parser)
    {
        if (null === static::$_instance) {
            static::$_instance = new static($stack, $parser);
        }

        return static::$_instance;
    }

    final public static function getResult()
    {
        if (null === static::$_instance) {
            throw new \Exception('The walker is paraplegic :(');
        }

        $snapshot = static::$_instance->_traveled->last();

        static::$_instance = null;

        return $snapshot;
    }

    final protected function getUnitOfWork()
    {
        if ($this->_traveled->isEmpty()) {
            return $this->_stack;
        }

         return  $this->_traveled->first();
    }

    public function walk($entry)
    {
        $walked = $this->aggregate($entry, $this->getUnitOfWork());

        if ($this->isVertical($walked)) {
            $this->walkVertically($walked);
        }

        if ($this->isHorizontal($walked)) {
            $this->walkHorizontally($walked);
        }
    }
}
