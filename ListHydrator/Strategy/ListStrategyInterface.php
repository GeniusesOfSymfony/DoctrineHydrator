<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Strategy;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\UnitOfWork;

/**
 * Class ListStrategyInterface
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Strategy
 */
interface ListStrategyInterface
{
    /**
     * @param       $PK
     * @param array $hints
     *
     * @return mixed
     */
    public function find(&$PK, Array &$hints, ResultSetMapping $rsm, UnitOfWork $uow);

    /**
     * @return mixed
     */
    public function getSolution();

    /**
     * @param Statement        $stmt
     * @param ResultSetMapping $rsm
     * @param UnitOfWork       $uow
     * @param array            $hints
     * @param                  $PK
     *
     * @return mixed
     */
    public function work(Statement $stmt, ResultSetMapping $rsm, UnitOfWork $uow, array &$hints, &$PK);

    /**
     * @return bool
     */
    public function isTraversableSolution();
}
