<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser;

/**
 * Class ListParserInterface
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser
 */
interface ListParserInterface extends ArrayPositionInterface, ArrayBrowserInterface,  \Iterator, \Countable,\ArrayAccess
{
    /**
     * @param $PK
     *
     * @return mixed
     */
    public function parse($field);

    /**
     * @param   $field
     * @param   $mode
     *
     * @return mixed
     */
    public function assign($field, $mode);

    /**
     * @return Array
     */
    public function toArray();

    /**
     * @return void
     */
    public function clear();

    /**
     * @param      $offset
     * @param null $length
     *
     * @return mixed
     */
    public function slice($offset, $length = null);
}
