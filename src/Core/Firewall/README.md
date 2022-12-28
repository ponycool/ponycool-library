## Basic usage
```
use PonyCool\Core\Firewall\Firewall;

$whiteList = array(
    '127.0.0.1',
    '192.168.0.*',
);

$blackList = array(
    '192.168.0.50',
);

$firewall = new Firewall();

$connAllowed = $firewall
    ->setDefaultState(false)
    ->addList($whiteList, 'local', true)
    ->addList($blackList, 'localBad', false)
    ->setIpAddress('195.88.195.146')
    ->handle()
;

if (!$connAllowed) {
    http_response_code(403); // Forbidden
    exit();
}
```
在本例中，防火墙只允许以192.168.0(而不是192.168.0.50)和127.0.0.1启动的ip。

在其他情况下，handle()返回false。



setDefaultState(false)定义默认防火墙响应(可选-默认为false)，

addList($white ist， 'local'， true)定义了$white ist列表，称为local as allowed (true)，

addList($黑名单的localBad假);定义$blackList list，称为localBad为已拒绝(false)


## Entries Formats
| Type        | Syntax                      | Details                                                      |
| :---------- | :-------------------------- | :----------------------------------------------------------- |
| IPV6        | `::1`                       | Short notation                                               |
| IPV4        | `192.168.0.1`               |                                                              |
| Range       | `192.168.0.0-192.168.1.60`  | Includes all IPs from *192.168.0.0* to *192.168.0.255* and from *192.168.1.0* to *198.168.1.60* |
| Wild card   | `192.168.0.*`               | IPs starting with *192.168.0* Same as IP Range `192.168.0.0-192.168.0.255` |
| Subnet mask | `192.168.0.0/255.255.255.0` | IPs starting with *192.168.0* Same as `192.168.0.0-192.168.0.255` and `192.168.0.*` |
| CIDR Mask   | `192.168.0.0/24`            | IPs starting with *192.168.0* Same as `192.168.0.0-192.168.0.255` and `192.168.0.*` and `192.168.0.0/255.255.255.0` |

## Custom error handling

```
use PonyCool\Core\Firewall\Firewall;

function handleFirewallReturn(Firewall $firewall, $response) {
    if (false === $response) {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbiden");
        exit();
    }

    return $response;
}

$whiteList = array(
    '127.0.0.1',
    '198.168.0.*',
);

$blackList = array(
    '192.168.0.50',
);

$firewall = new Firewall();
$firewall
    ->setDefaultState(true)
    ->addList($whiteList, 'local', true)
    ->addList($blackList, 'localBad', false)
    ->setIpAddress('195.88.195.146')
    ->handle('handleFirewallReturn')
;
```
handle('handleFirewallReturn')使用防火墙对象和响应作为参数调用handleFirewallReturn (true或false)。