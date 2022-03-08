<?php


namespace PonyCool\Core\Firewall\Entry;


abstract class AbstractEntry implements EntryInterface
{
    protected string $template;

    public function __construct($entry)
    {
        $this->template = $entry;
    }
}
