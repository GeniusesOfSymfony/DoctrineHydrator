<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Adapter;

use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use Gos\Component\DoctrineHydrator\ListHydrator\AbstractListHydrator;
use Gos\Component\DoctrineHydrator\ListHydrator\Parser\ListParserInterface;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategy;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategyInterface;

/**
 * Class ListArrayAdapter
 * @package AQF\CoreBundle\Hydrators\ListHydrator\Adapter
 */
class ListArrayAdapter extends ArrayHydrator implements ListAdapterInterface
{
    /**
     * @var
     */
    protected $_strategy;

    /**
     * @param ListStrategyInterface $strategy lol
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
            if (!is_array($map)) {
                $map = array();
            }

            $map[] = $data;
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
     * @param $data
     * @return mixed
     */
    public function getIndexRow($data, ListParserInterface $parser)
    {
        $PK = $this->_hints[AbstractListHydrator::HINT_LIST_FIELD];

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
                if (isset($data[$PK])) {
                    return $data[$PK];
                }

                throw new \Exception(sprintf('The key %s do not exist among of the following %s', $PK, array_keys($data)));
        }
    }

    /**
     * @param $map
     */
    public function adaptMap($map)
    {
        if (true === $this->_hints[AbstractListHydrator::HINT_UNIQUE_ASSOCIATION]) {
            $adaptedMap = array();
            foreach ($map as $key => $element) {
                $adaptedMap[$key] = current($element);
            }
        } else {
            $adaptedMap = $map;
        }

        return $adaptedMap;
    }
}
