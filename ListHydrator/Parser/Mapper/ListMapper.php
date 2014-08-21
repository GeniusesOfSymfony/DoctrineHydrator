<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper;

use Gos\Component\DoctrineHydrator\ListHydrator\AbstractListHydrator;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategy;

/**
 * Class ListMapper
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper
 */
class ListMapper extends AbstractListMapper
{
    /**
     * @param   $reference
     * @param   $key
     * @param   $data
     *
     * @throws \Gos\Component\DoctrineHydrator\ListHydrator\Exception\BadMethodCallException
     * @throws \Exception
     */
    public function map($reference, $key, $data, \Closure $compositeBehavior = null)
    {
        if (is_scalar($key)) {
            $this->_map[$key][] = $data;
        } else {
            if ($this->_strategy->isTraversableSolution()) {
                $this->handle($key, $data, $compositeBehavior);
            }
        }
    }

    /**
     * @param          $key
     * @param          $data
     * @param \Closure $compositeBehavior
     */
    protected function handle($key, $data, \Closure $compositeBehavior = null)
    {
        $map =& $this->_map;

        if ($this->_strategy->getSolution() == ListStrategy::COMPOSITE_PREDICT_PK) {
            $stringKey = $this->compositeArrayKeysToString($key, $compositeBehavior);
            $map =& $map[$stringKey];
            $this->_hydrator->mapFallback($map, $data);
        } else {
            foreach ($key as $depth => $element) {
                if (!isset($map[$element])) {
                    $map[$element] = [];
                }

                $map =& $map[$element];

                if ($depth == (count($key) - 1 )) {
                    $this->_hydrator->mapFallback($map, $data);
                }
            }
        }
    }

    /**
     * @param $key
     * @param null|\Closure $compositeBehavior
     *
     * @return string
     * @throws \Exception
     */
    protected function compositeArrayKeysToString(&$key, $compositeBehavior)
    {
        if (null !== $compositeBehavior) {
            if (is_object($compositeBehavior) && $compositeBehavior instanceof \Closure) {
                return $compositeBehavior($key);
            }

            throw new \Exception(sprintf('The hint %s must contain a closure, %s given', AbstractListHydrator::HINT_COMPOSITE_PK_BEHAVIOR, gettype($compositeBehavior)));

        } else {
            return implode('-', $key);
        }
    }

    /**
     * @return mixed
     */
    public function buildMap()
    {
        return $this->_hydrator->adaptMap($this->_map);
    }
}
