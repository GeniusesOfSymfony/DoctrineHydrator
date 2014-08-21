<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper;

use Gos\Component\DoctrineHydrator\ListHydrator\Adapter\ListAdapterInterface;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategyInterface;

/**
 * Class AbstractListMapper
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper
 */
abstract class AbstractListMapper implements ListMapperInterface
{
    /**
     * @var
     */
    protected $_strategy;

    /**
     * @var
     */
    protected $_mode;

    /**
     * @var
     */
    protected $_hydrator;

    /**
     * @var array
     */
    protected $_map = [];

    /**
     * @var array
     */
    public $keys = [];

    /**
     * @param $mode
     */
    public function __construct($mode, ListAdapterInterface $hydrator)
    {
        $this->_mode = $mode;
        $this->_hydrator = $hydrator;
    }

    /**
     * @param ListStrategyInterface $strategy
     */
    public function setStrategy(ListStrategyInterface $strategy)
    {
        $this->_strategy = $strategy;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->buildMap();
    }
}
