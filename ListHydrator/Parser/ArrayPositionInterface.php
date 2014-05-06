<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser;

/**
 * Interface ArrayPosition
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser
 */
interface ArrayPositionInterface
{
    /**
     * @return mixed
     */
    public function first();

    /**
     * @return mixed
     */
    public function last();
}
