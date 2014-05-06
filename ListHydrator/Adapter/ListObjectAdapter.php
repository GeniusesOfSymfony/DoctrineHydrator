<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
use Gos\Component\DoctrineHydrator\ListHydrator\AbstractListHydrator;
use Gos\Component\DoctrineHydrator\ListHydrator\Parser\ListParserInterface;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategy;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategyInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class ListObjectAdapter
 * @package AQF\CoreBundle\Hydrators\ListHydrator\Adapter
 */
class ListObjectAdapter extends ObjectHydrator implements ListAdapterInterface
{
    /**
     * @param $data
     * @return mixed
     */
    protected $_strategy;

    /**
     * @param ListStrategyInterface $strategy
     */
    public function setStrategy(ListStrategyInterface $strategy)
    {
        $this->_strategy = $strategy;
    }

    /**
     * @param $map
     * @param $data
     */
    public function mapFallback(&$map, $data)
    {
        if (false === $this->_strategy->isPredictableSolution()) {
            if (!$map instanceof ArrayCollection) {
                $map = new ArrayCollection();
            }

            $map->add($data);
        } else {
            $map = $data;
        }
    }

    /**
     * @param $data
     * @param $parser
     *
     * @return mixed
     */
    protected function walkProcess($data, ListParserInterface $parser)
    {
        $staticWalker = $parser->getStaticWalker();

        $parser->walk(function ($method) use ($data, $parser) {
            $walker = $parser->getWalker($data);
            $walker->walk($method);
        });

        return $staticWalker::getResult();
    }

    /**
     * @param                     $data
     * @param ListParserInterface $parser
     *
     * @return array|mixed
     */
    public function getIndexRow($data, ListParserInterface $parser)
    {
        switch ($this->_strategy->getSolution()) {
            case ListStrategy::OVERRIDE_DEEP_PK:
                return $this->walkProcess($data, $parser);
            case ListStrategy::COMPOSITE_PREDICT_PK:
            case ListStrategy::COMPOSITE_PK:
                $buffer = array();
                foreach ($parser as $parse) {
                    $buffer[] = $this->walkProcess($data, $parse);
                }

                return $buffer;
            default:
                $PK = $this->_hints[AbstractListHydrator::HINT_LIST_FIELD];
                $accessor = PropertyAccess::createPropertyAccessor();

                return $accessor->getValue($data, $PK);
        }
    }

    /**
     * @param $map
     */
    public function adaptMap($map)
    {
        $adaptedMap = array();

        foreach ($map as $key => $elements) {
            if (true === $this->_hints[AbstractListHydrator::HINT_UNIQUE_ASSOCIATION]) {
                $adaptedMap[$key] = (is_array($elements)) ? current($elements) : $elements;
            } else {
                $adaptedMap[$key] = (is_array($elements)) ? new ArrayCollection($elements) : $elements;
            }

        }

        return $adaptedMap;
    }
}
