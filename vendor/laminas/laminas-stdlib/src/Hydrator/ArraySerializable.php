<?php

/**
 * @see       https://github.com/laminas/laminas-stdlib for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stdlib/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stdlib/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stdlib\Hydrator;

use Laminas\Stdlib\Exception;

class ArraySerializable extends AbstractHydrator
{
    /**
     * Extract values from the provided object
     *
     * Extracts values via the object's getArrayCopy() method.
     *
     * @param  object $object
     * @return array
     * @throws Exception\BadMethodCallException for an $object not implementing getArrayCopy()
     */
    public function extract($object)
    {
        if (!is_callable([$object, 'getArrayCopy'])) {
            throw new Exception\BadMethodCallException(
                sprintf('%s expects the provided object to implement getArrayCopy()', __METHOD__)
            );
        }

        $data = $object->getArrayCopy();
        $filter = $this->getFilter();

        foreach ($data as $name => $value) {
            if (!$filter->filter($name)) {
                unset($data[$name]);
                continue;
            }
            $extractedName = $this->extractName($name, $object);
            // replace the original key with extracted, if differ
            if ($extractedName !== $name) {
                unset($data[$name]);
                $name = $extractedName;
            }
            $data[$name] = $this->extractValue($name, $value, $object);
        }

        return $data;
    }

    /**
     * Hydrate an object
     *
     * Hydrates an object by passing $data to either its exchangeArray() or
     * populate() method.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     * @throws Exception\BadMethodCallException for an $object not implementing exchangeArray() or populate()
     */
    public function hydrate(array $data, $object)
    {
        $replacement = [];
        foreach ($data as $key => $value) {
            $name = $this->hydrateName($key, $data);
            $replacement[$name] = $this->hydrateValue($name, $value, $data);
        }

        if (is_callable([$object, 'exchangeArray'])) {
            $object->exchangeArray($replacement);
        } elseif (is_callable([$object, 'populate'])) {
            $object->populate($replacement);
        } else {
            throw new Exception\BadMethodCallException(
                sprintf('%s expects the provided object to implement exchangeArray() or populate()', __METHOD__)
            );
        }
        return $object;
    }
}
