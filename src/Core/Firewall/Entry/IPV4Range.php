<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;

use PonyCool\Core\Firewall\Entry\Traits\IPRange;

class IPV4Range extends IPV4
{
    use IPRange;

    public static string $separatorRegex = '(\s*)\-(\s*)';
}