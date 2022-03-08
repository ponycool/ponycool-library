<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;

use PonyCool\Core\Firewall\Entry\Traits\IPCIDR;

class IPV6CIDR extends IPV6Mask
{
    use IPCIDR;
}
