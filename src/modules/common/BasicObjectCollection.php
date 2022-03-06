<?php

namespace modules\common;

class BasicObjectCollection extends \ArrayObject
{
    public function add($basicObject, $index = null)
    {
        if (null !== $index) {
            $this->offsetSet($index, $basicObject);
        } else {
            $this->append($basicObject);
        }
    }

    public function hasObjects(): bool
    {
        if ($this->count() > 0) {
            return true;
        }

        return false;
    }

    public function toArray($assoc = true): array
    {
        $collection = [];

        foreach ($this->getArrayCopy() as $label => $item) {
            $item = $item->toArray();
            if (true === $assoc) {
                $collection[$label] = $item;
            } else {
                $collection[] = $item;
            }
        }
        return $collection;
    }

}
