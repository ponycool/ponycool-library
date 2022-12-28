<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;


interface EntryInterface
{
    public static function match(string $entry);

    public function check(string $entry);

    public function getMatchingEntries();
}