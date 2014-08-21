<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * Class ListHydrator
 * @package AQF\CoreBundle\Hydrators
 */
abstract class AbstractListHydrator extends AbstractHydrator
{
    /**
     * @var null
     */
    protected $_parser = null;

    /**
     * @var null
     */
    protected $_factory = null;

    /**
     * @var
     */
    protected $_mode;

    /**
     * @var
     */
    protected $_hydrator;

    /**
     * @var null
     */
    protected $_PK = null;

    /**
     * @var
     */
    protected $_strategy;

    const HINT_LIST_FIELD = 'doctrine.list.field';
    const HINT_COMPOSITE_PK_BEHAVIOR = 'doctrine.multi.pk.behavior';
    const HINT_UNIQUE_ASSOCIATION = 'doctrine.list_association.unique';

    /**
     * @param EntityManager $em
     * @param               $hydrationMode
     * @param               $listHydratorFactory
     */
    public function __construct(EntityManager $em, $mode)
    {
        $this->_mode = $mode;

        parent::__construct($em);
    }

    /**
     * @return array
     */
    protected function hydrateAllData()
    {
        $this->normalizeHints();

        $this->_strategy->work($this->_rsm, $this->_uow, $this->_hints, $this->_PK);

        $this->_hydrator->setStrategy($this->_strategy);

        $this->_parser->setStrategy($this->_strategy);

        $this->_parser->assign($this->_PK, $this->_mode);

        $rows = $this->_hydrator->hydrateAll($this->_stmt, $this->_rsm, $this->_hints);

        $cache = $result = [];

        $this->hydrateRowData($rows, $cache, $result);

        return $result;
    }

    /**
     * Normalize hints
     */
    protected function normalizeHints()
    {
        if (!isset($this->_hints[self::HINT_COMPOSITE_PK_BEHAVIOR])) {
            $this->_hints[self::HINT_COMPOSITE_PK_BEHAVIOR] = null;
        }

        if (!isset($this->_hints[self::HINT_UNIQUE_ASSOCIATION])) {
            $this->_hints[self::HINT_UNIQUE_ASSOCIATION] = false;
        }
    }

    /**
     * @param array $rows
     * @param array $cache
     * @param array $result
     *
     * @return array|void
     */
    protected function hydrateRowData(array $rows, array &$cache, array &$result)
    {
        $mapper = $this->_parser->getMapper();

        foreach ($rows as $reference => $data) {
            $mapper->map($reference, $this->_hydrator->getIndexRow($data, $this->_parser), $data, $this->_hints[self::HINT_COMPOSITE_PK_BEHAVIOR]);
        }

        $result = $mapper->getMap();
    }
}
