<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Gos\Component\DoctrineHydrator\ListHydrator\Factory\ListHydratorFactory;
use Gos\Component\DoctrineHydrator\ListHydrator\Parser\ListParser;
use Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper\ListMapper;
use Gos\Component\DoctrineHydrator\ListHydrator\Strategy\ListStrategy;

/**
 * Class ArrayListHydrator
 * @package AQF\CoreBundle\Hydrators\ListHydrator
 */
class ArrayListHydrator extends AbstractListHydrator
{
    const HYDRATE_ARRAY_LIST = 'ArrayListHydrator';

    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Query::HYDRATE_ARRAY);
        $this->_factory = new ListHydratorFactory($em);
        $this->_hydrator = $this->_factory->createNamed(Query::HYDRATE_ARRAY);
        $this->_strategy = new ListStrategy();
        $this->_parser = new ListParser();

        $listMapper = new ListMapper(Query::HYDRATE_ARRAY, $this->_hydrator);

        $this->_parser->setMapper($listMapper);
        $this->_parser->registerWalker([
                Query::HYDRATE_ARRAY => 'Gos\Component\DoctrineHydrator\ListHydrator\Parser\Walker\ArrayWalker',
        ]);
    }
}
