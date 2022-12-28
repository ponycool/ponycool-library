<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall\Entry;

use PonyCool\Core\Firewall\Entry\Traits\IPCIDR;

class IPV4CIDR extends IPV4Mask
{
    use IPCIDR;
}