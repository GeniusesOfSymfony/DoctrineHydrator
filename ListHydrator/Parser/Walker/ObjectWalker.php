<?php
namespace Gos\Component\DoctrineHydrator\ListHydrator\Parser\Walker;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class ObjectWalker extends AbstractWalker
{
    protected function aggregate($target, $uow)
    {
        if (method_exists($uow, 'get'.ucfirst($target))) {
            return $this->_accessor->getValue($uow, $target);
        } else {
            return $uow;
        }
    }

    protected function isVertical($walked)
    {
        return ($walked instanceof PersistentCollection || $walked instanceof ArrayCollection);
    }

    protected function isHorizontal($walked)
    {
        return is_object($walked) || is_scalar($walked);
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
        throw new \Exception('You can not use hydrate behavior on collection');
    }
}
