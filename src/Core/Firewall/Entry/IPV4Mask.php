<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;

use PonyCool\Core\Firewall\Entry\Traits\IPMask;

class IPV4Mask extends IPV4Range
{
    use IPMask;

    public static string $separatorRegex = '(\s*)\/(\s*)';
}