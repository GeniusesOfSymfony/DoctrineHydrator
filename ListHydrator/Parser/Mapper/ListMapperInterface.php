<?php

namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper;

/**
 * Class ListMapperInterface
 * @package Gos\Component\DoctrineHydrator\ListHydrator\Parser\Mapper
 */
interface ListMapperInterface
{
    public function map($reference, $key, $data);

    public function buildMap();
}
