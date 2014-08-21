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
class ObjectListHydrator extends AbstractListHydrator
{
    const HYDRATE_OBJECT_LIST = 'ObjectListHydrator';

    public function __construct(EntityManager $em)
    {
        $this->_factory = new ListHydratorFactory($em);
        $this->_hydrator = $this->_factory->createNamed(Query::HYDRATE_OBJECT);
        $this->_strategy = new ListStrategy();
        $parser = new ListParser();
        $listMapper = new ListMapper(Query::HYDRATE_OBJECT, $this->_hydrator);
        $parser->registerWalker([
            Query::HYDRATE_OBJECT => 'Gos\Component\DoctrineHydrator\ListHydrator\Parser\Walker\ObjectWalker'
        ]);
        $parser->setMapper($listMapper);
        $this->_parser = $parser;
        parent::__construct($em, Query::HYDRATE_OBJECT);
    }
}
