<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Lists;

class ListMerger
{
    private array $lists = [];

    /**
     * 增加列表
     * @param EntryList $entryList
     * @param string $name
     * @return $this
     */
    public function addList(EntryList $entryList, string $name): ListMerger
    {
        $this->lists[$name] = $entryList;

        return $this;
    }

    /**
     * 检查是否允许
     * @param string $entry
     * @param bool $defaultState
     * @return bool
     */
    public function isAllowed(string $entry, bool $defaultState): bool
    {
        $whited = false;
        $blacked = false;

        if (is_array($this->lists)) {
            foreach ($this->lists as $list) {
                $allowed = $list->isAllowed($entry);
                if ($allowed !== null) {
                    if ($allowed) {
                        $whited = true;
                    } else {
                        $blacked = true;
                    }
                }
            }
        }

        return ($defaultState ? (!$blacked || $whited) : ($whited && !$blacked));
    }
}