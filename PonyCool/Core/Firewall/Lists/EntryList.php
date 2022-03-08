<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Lists;


class EntryList
{
    protected array $entries;
    protected ?bool $matchingResponse;
    protected array $matchingEntries;

    public function __construct(array $list = array(), bool $trusted = false)
    {
        $this->entries = $list;
        $this->matchingResponse = ($trusted === true);
    }

    /**
     * 是否允许该条规则
     * @param string $entry
     * @return bool|null TRUE = allowed, FALSE = rejected, NULL = not handled
     */
    public function isAllowed(string $entry)
    {
        foreach ($this->entries as $item) {
            if ($item->check($entry)) {
                return $this->matchingResponse;
            }
        }
        return null;
    }

    /**
     * 解析所有匹配的条目
     * @return array
     */
    public function getMatchingEntries(): array
    {
        if ($this->matchingEntries === null) {
            $this->matchingEntries = [];
            foreach ($this->entries as $entry) {
                $this->matchingEntries = array_merge($this->matchingEntries, $entry->getMatchingEntries());
            }
        }

        return $this->matchingEntries;
    }
}
