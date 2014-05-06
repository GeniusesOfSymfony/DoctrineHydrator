<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Adapter;

use Gos\Component\DoctrineHydrator\ListHydrator\Parser\ListParserInterface;

/**
 * Class ListAdapterInterface
 * @package AQF\CoreBundle\Hydrators\ListHydrator\Adapter
 */
interface ListAdapterInterface
{
    /**
     * @param $data
     *
     * @return mixed
     */
    public function getIndexRow($data, ListParserInterface $parser);

    /**
     * @param $map
     *
     * @return mixed
     */
    public function adaptMap($map);

    /**
     * @param $map
     * @param $data
     *
     * @return mixed
     */
    public function mapFallback(&$map, $data);
}
