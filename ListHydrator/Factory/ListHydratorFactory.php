<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Gos\Component\DoctrineHydrator\ListHydrator\Adapter\ListArrayAdapter;
use Gos\Component\DoctrineHydrator\ListHydrator\Adapter\ListObjectAdapter;

/**
 * Class ListFactory
 * @package AQF\CoreBundle\Hydrators\ListHydrator\Factory
 */
class ListHydratorFactory implements ListFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @param $name
     * @param $em
     *
     * @return ListArrayAdapter|ListObjectAdapter
     */
    public function createNamed($name)
    {
        switch ($name) {
            case Query::HYDRATE_ARRAY:
                return new ListArrayAdapter($this->_em);
            case Query::HYDRATE_OBJECT:
                return new ListObjectAdapter($this->_em);
        }
    }
}
