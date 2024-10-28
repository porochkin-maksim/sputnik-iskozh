<?php declare(strict_types=1);

namespace Core\Services\Files\Factories;

use Core\Services\Files\Models\MetaData;

class MetaDataFactory
{
    /**
     * @param bool|resource $tmpHandle
     *
     * @return MetaData|null
     */
    public function make(mixed $tmpHandle): ?MetaData
    {
        try {
            $data = $tmpHandle ? stream_get_meta_data($tmpHandle) : null;
        }
        catch (\Exception) {
            return null;
        }

        if ($data === null) {
            return null;
        }

        return new MetaData(
            array_key_exists('timed_out', $data) ? (bool) $data['timed_out'] : null,
            array_key_exists('blocked', $data) ? (bool) $data['blocked'] : null,
            array_key_exists('eof', $data) ? (bool) $data['eof'] : null,
            array_key_exists('wrapper_type', $data) ? (string) $data['wrapper_type'] : null,
            array_key_exists('stream_type', $data) ? (string) $data['stream_type'] : null,
            array_key_exists('mode', $data) ? (string) $data['mode'] : null,
            array_key_exists('unread_bytes', $data) ? (int) $data['unread_bytes'] : null,
            array_key_exists('seekable', $data) ? (bool) $data['seekable'] : null,
            array_key_exists('uri', $data) ? (string) $data['uri'] : null,
        );
    }
}
