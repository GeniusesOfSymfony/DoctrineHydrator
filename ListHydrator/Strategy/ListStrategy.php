<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Strategy;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\UnitOfWork;
use Gos\Component\DoctrineHydrator\ListHydrator\AbstractListHydrator;

/**
 * Class ListStrategy
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Strategy
 */
class ListStrategy extends AbstractListStrategy
{
    /**
     * Pool of available strategy
     */
    const PREDICT_PK = 'predict_PK';
    const COMPOSITE_PREDICT_PK = 'composite_predict_PK';
    const OVERRIDE_PK = 'override_PK';
    const OVERRIDE_DEEP_PK = 'override_deep_PK';
    const COMPOSITE_PK = 'composite_PK';

    protected $predicatedBuffer = null;
    protected $identifierBuffer = null;

    /**
     * @param       $PK
     * @param array $hints
     */
    public function find(&$PK, array &$hints, ResultSetMapping $rsm, UnitOfWork $uow)
    {

        if (isset($hints[AbstractListHydrator::HINT_LIST_FIELD])) {
            $PK = $hints[AbstractListHydrator::HINT_LIST_FIELD];

            if (is_string($PK)) {
                $this->_solution = (strpos($PK, '.') === false) ? self::OVERRIDE_PK : self::OVERRIDE_DEEP_PK;

                return true;
            }

            if (is_array($PK)) {
                if (count($PK) == 1) {
                    $hints[AbstractListHydrator::HINT_LIST_FIELD] = current($PK);

                    return $this->find($PK, $hints, $rsm, $uow);
                } else {
                    $this->_solution = self::COMPOSITE_PK;
                }

                return true;
            }
        } else {
            if (1 === count($this->getIdentifier($rsm, $uow))) {
                $this->_solution = self::PREDICT_PK;
            } else {
                $this->_solution = self::COMPOSITE_PREDICT_PK;
            }

            return true;
        }

        return false;
    }

    /**
     * @param ResultSetMapping $rsm
     * @param UnitOfWork       $uow
     *
     * @return array|null|string
     */
    protected function getIdentifier(ResultSetMapping $rsm, UnitOfWork $uow)
    {
        if (null === $this->identifierBuffer) {
            $this->identifierBuffer = $uow->getEntityPersister(current($rsm->getAliasMap()))->getClassMetadata()->getIdentifier();
        }

        return $this->identifierBuffer;
    }

    /**
     * @param ResultSetMapping $rsm
     * @param UnitOfWork       $uow
     * @param array            $hints
     *
     * @return mixed|string
     * @throws \Exception
     */
    protected function predictStrategy(ResultSetMapping $rsm, UnitOfWork $uow, array $hints)
    {
        if (null == $this->predicatedBuffer) {

            $identifier = $this->getIdentifier($rsm, $uow);
            //A PK can be composite
            if (1 === count($identifier)) {
                $PK = current(array_values($identifier));
            } else {
                $PK = $identifier;
            }

            $this->predicatedBuffer = $PK;
        }

        return $this->predicatedBuffer;
    }

    /**
     * @param ResultSetMapping $rsm
     * @param UnitOfWork       $uow
     * @param array            $hints
     * @param                  $PK
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function work(ResultSetMapping $rsm, UnitOfWork $uow, array &$hints, &$PK)
    {
        if (null === $this->getSolution() && null === $this->find($PK, $hints, $rsm, $uow)) {
            throw new \Exception('Unable to find a strategy to process');
        }

        //In the case of we does not define a PK, make a predicate based on scheme table.
        if (self::PREDICT_PK === $this->getSolution() xor self::COMPOSITE_PREDICT_PK === $this->getSolution()) {
            $PK = $this->predictStrategy($rsm, $uow, $hints);
        }

        if (null === static::$_fallback) {
            $this->predictStrategy($rsm, $uow, $hints);
        }

        //Sync hint for the parent hydrator
        $hints[AbstractListHydrator::HINT_LIST_FIELD] = $PK;
    }

    /**
     * @return bool
     */
    public function isTraversableSolution()
    {
        $traversableSolution = [self::COMPOSITE_PREDICT_PK, self::COMPOSITE_PK];

        return in_array($this->getSolution(), $traversableSolution);
    }

    public function isPredictableSolution()
    {
        $predictableSolution = [self::COMPOSITE_PREDICT_PK, self::PREDICT_PK];

        return in_array($this->getSolution(), $predictableSolution);
    }
}
