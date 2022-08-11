<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;

use PonyCool\Core\Firewall\Lists\EntryList;

class EntryFactory
{
    protected array $classes;

    public function __construct(?array $classes = null)
    {
        if (is_null($classes)) {
            $this->classes = [
                __NAMESPACE__ . '\IPV4',
                __NAMESPACE__ . '\IPV4CIDR',
                __NAMESPACE__ . '\IPV4Mask',
                __NAMESPACE__ . '\IPV4Range',
                __NAMESPACE__ . '\IPV4Wildcard',
                __NAMESPACE__ . '\IPV6',
                __NAMESPACE__ . '\IPV6CIDR',
                __NAMESPACE__ . '\IPV6Mask',
                __NAMESPACE__ . '\IPV6Range',
                __NAMESPACE__ . '\IPV6Wildcard',
            ];
        } else {
            $this->classes = $classes;
        }
    }

    /**
     * 获取条目
     * @param string $entry
     * @return bool|mixed
     */
    public function getEntry(string $entry): mixed
    {
        foreach ($this->classes as $class) {
            if ($class::match($entry)) {
                return new $class($entry);
            }
        }
        return false;
    }

    /**
     * 获取条目列表
     * @param array $list
     * @param bool $trusted
     * @return EntryList
     */
    public function getEntryList(array $list, bool $trusted): EntryList
    {
        $flatten = [];
        $this->flattenArray($list, $flatten);
        $entries = [];
        foreach ($flatten as $item) {
            $entry = $this->getEntry($item);
            if ($entry) {
                $entries[] = $entry;
            }
        }
        return new EntryList($entries, $trusted);
    }

    /**
     * 展平数组
     * @param array $source
     * @param array $dest
     */
    protected function flattenArray(array $source, array &$dest): void
    {
        foreach ($source as $item) {
            if (is_array($item)) {
                $this->flattenArray($item, $dest);
            } else {
                $dest[] = $item;
            }
        }
    }
}