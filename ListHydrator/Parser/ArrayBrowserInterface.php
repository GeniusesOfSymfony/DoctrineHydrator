<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser;

/**
 * Interface ArrayBrowserInterface
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser
 */
interface ArrayBrowserInterface
{
    /**
     * @param \Closure $p
     *
     * @return mixed
     */
    public function map(\Closure $p);

    /**
     * @param \Closure $p
     *
     * @return mixed
     */
    public function walk(\Closure $p);

    /**
     * @param \Closure $p
     *
     * @return mixed
     */
    public function filter(\Closure $p);
}
