<?php declare(strict_types=1);

namespace html_go;

use html_go\exceptions\InternalException;

abstract class Utils
{
    /**
     * Merge a <code>stdClass</code> object with an array to produce a new <code>stdClass</code>.
     * @param \stdClass $object
     * @param array<mixed> $data
     * @throws InternalException
     * @return \stdClass
     */
    public static function mergeArrayIntoStdClass(\stdClass $object, array $data): \stdClass {
        if (($json = json_encode($object)) === false) {
            throw new InternalException('json_encode failed!');
        }
        return (object)\array_merge(json_decode($json, true), $data);
    }
}
