<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser\Walker;

use Doctrine\Common\Collections\Collection;

class ArrayWalker extends AbstractWalker
{
    protected function aggregate($entry, $uow)
    {
        if (is_array($uow)) {
            if (array_key_exists($entry, $uow)) {
                return $uow[$entry];
            }
        } else {
            return $uow;
        }
    }

    protected function isVertical($walked)
    {
        return is_array($walked);
    }

    protected function isHorizontal($walked)
    {
        return is_scalar($walked);
    }

    /**
     * @param $walked
     */
    public function walkHorizontally($walked)
    {
        $this->_traveled->add($walked);
    }

    /**
     * @param Collection $walked
     */
    public function walkVertically($walked)
    {
        $this->_traveled->add($walked);
    }
}
