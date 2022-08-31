<?php
declare(strict_types=1);

namespace PonyCool\Apollo;

use Closure;
use Exception;

class Client
{
    // apollo服务端地址
    protected string $configServer = '';
    // apollo配置项目的appId
    protected string $appId = '';
    protected string $cluster = 'default';
    // apollo配置项目的namespace
    protected array $namespaces = [];
    // 绑定IP做灰度发布用
    protected string $clientIp = '127.0.0.1';
    protected array $notifications = [];
    // 获取某个namespace配置的请求超时时间
    protected int $pullTimeout = 10;
    // 每次请求获取apollo配置变更时的超时时间
    protected int $intervalTimeout = 60;
    // apollo访问密钥
    protected string $secret;
    // 配置保存目录
    public string $savePath;
    // 时间戳
    private int $timestamp;
    // 请求头信息
    private array $header = [];

    /**
     * 初始化
     * Client constructor
     */
    public function __construct()
    {
        $this->timestamp = $this->getMillisecond();
        $this->savePath = dirname($_SERVER['SCRIPT_FILENAME']);
    }

    /**
     * 初始化客户端
     * @throws Exception
     */
    public function init(): void
    {
        try {
            if (empty($this->configServer)) {
                throw new Exception('配置服务中心地址未配置');
            }
            if (empty($this->appId)) {
                throw new Exception('AppID未配置');
            }
            if (count($this->namespaces) === 0) {
                throw new Exception('NameSpaces未配置');
            }
            foreach ($this->namespaces as $namespace) {
                $this->notifications[$namespace] = ['namespaceName' => $namespace, 'notificationId' => -1];
            }
        } catch (Exception $e) {
            throw new Exception(sprintf('配置服务中心客户端初始化失败，%s', $e->getMessage()));
        }
    }

    /**
     * @return string
     */
    public function getConfigServer(): string
    {
        return $this->configServer;
    }

    /**
     * @param string $configServer
     * @return $this
     */
    public function setConfigServer(string $configServer): Client
    {
        $this->configServer = $configServer;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     * @return $this
     */
    public function setAppId(string $appId): Client
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCluster(): string
    {
        return $this->cluster;
    }

    /**
     * @param string $cluster
     * @return $this
     */
    public function setCluster(string $cluster): Client
    {
        $this->cluster = $cluster;
        return $this;
    }

    /**
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * @param array $namespaces
     * @return $this
     */
    public function setNamespaces(array $namespaces): Client
    {
        $this->namespaces = $namespaces;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     * @return $this
     */
    public function setClientIp(string $clientIp): Client
    {
        $this->clientIp = $clientIp;
        return $this;
    }

    /**
     * @return int
     */
    public function getPullTimeout(): int
    {
        return $this->pullTimeout;
    }

    /**
     * @param int $pullTimeout
     * @return $this
     */
    public function setPullTimeout(int $pullTimeout): Client
    {
        $this->pullTimeout = $pullTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getIntervalTimeout(): int
    {
        return $this->intervalTimeout;
    }

    /**
     * @param int $intervalTimeout
     * @return $this
     */
    public function setIntervalTimeout(int $intervalTimeout): Client
    {
        $this->intervalTimeout = $intervalTimeout;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return $this
     */
    public function setSecret(string $secret): Client
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->savePath;
    }

    /**
     * @param string $savePath
     * @return $this
     */
    public function setSavePath(string $savePath): Client
    {
        $this->savePath = $savePath;
        return $this;
    }

    /**
     * 获取请求头
     * @param string $url
     * @return array
     */
    public function getHeader(string $url): array
    {
        // 初始化请求头
        if (!empty($this->secret)) {
            $urlInfo = parse_url($url);
            $pathWithQuery = $urlInfo['path'];
            if (!empty($urlInfo['query'])) {
                $pathWithQuery .= '?' . $urlInfo['query'];
            }
            $signature = Signature::getAuthorizationString(
                $this->appId,
                $this->timestamp,
                $pathWithQuery,
                $this->secret
            );
            $this->header = [
                sprintf('%s: %s', Signature::HTTP_HEADER_AUTHORIZATION, $signature),
                sprintf('%s: %s', Signature::HTTP_HEADER_TIMESTAMP, $this->timestamp),
            ];
        }
        return $this->header;
    }


    private function _getReleaseKey($config_file): string
    {
        $releaseKey = '';
        if (file_exists($config_file)) {
            $last_config = require $config_file;
            is_array($last_config) && isset($last_config['releaseKey']) && $releaseKey = $last_config['releaseKey'];
        }
        return $releaseKey;
    }

    /**
     * 获取单个namespace的配置文件路径
     * @param string $namespaceName
     * @return string
     */
    public function getConfigFile(string $namespaceName): string
    {
        return $this->savePath . DIRECTORY_SEPARATOR . 'apolloConfig.' . $namespaceName . '.php';
    }

    /**
     * 获取单个namespace的配置-无缓存的方式
     * @param string $namespaceName
     * @return bool
     * @throws Exception
     */
    public function pullConfig(string $namespaceName): bool
    {
        try {
            $this->init();
        } catch (Exception $e) {
            throw new Exception($e);
        }

        if (!file_exists($this->savePath)) {
            mkdir($this->savePath);
        }

        $base_api = rtrim($this->configServer, '/') . '/configs/' . $this->appId . '/' . $this->cluster . '/';
        $api = $base_api . $namespaceName;

        $args = [];
        $args['ip'] = $this->clientIp;
        $config_file = $this->getConfigFile($namespaceName);
        $args['releaseKey'] = $this->_getReleaseKey($config_file);

        $api .= '?' . http_build_query($args);

        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->pullTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (empty($this->secret)) {
            curl_setopt($ch, CURLOPT_HEADER, false);
        } else {
            $header = $this->getHeader($api);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode == 200) {
            $result = json_decode($body, true);
            $content = '<?php return ' . var_export($result, true) . ';';
            file_put_contents($config_file, $content);
        } elseif ($httpCode != 304) {
            echo $body ?: $error . "\n";
            return false;
        }
        return true;
    }

    /**
     * 获取多个namespace的配置-无缓存的方式
     * @param array $namespaceNames
     * @return array
     * @throws Exception
     */
    public function pullConfigBatch(array $namespaceNames): array
    {
        try {
            $this->init();
        } catch (Exception $e) {
            throw new Exception($e);
        }
        if (!$namespaceNames) return [];
        $multi_ch = curl_multi_init();
        $request_list = [];
        $base_url = rtrim($this->configServer, '/') . '/configs/' . $this->appId . '/' . $this->cluster . '/';
        $query_args = [];
        $query_args['ip'] = $this->clientIp;
        foreach ($namespaceNames as $namespaceName) {
            $request = [];
            $config_file = $this->getConfigFile($namespaceName);
            $request_url = $base_url . $namespaceName;
            $query_args['releaseKey'] = $this->_getReleaseKey($config_file);
            $query_string = '?' . http_build_query($query_args);
            $request_url .= $query_string;
            $ch = curl_init($request_url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->pullTimeout);
            if (empty($this->secret)) {
                curl_setopt($ch, CURLOPT_HEADER, false);
            } else {
                $header = $this->getHeader($request_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $request['ch'] = $ch;
            $request['config_file'] = $config_file;
            $request_list[$namespaceName] = $request;
            curl_multi_add_handle($multi_ch, $ch);
        }

        $active = null;
        // 执行批处理句柄
        do {
            $mrc = curl_multi_exec($multi_ch, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multi_ch) == -1) {
                usleep(100);
            }
            do {
                $mrc = curl_multi_exec($multi_ch, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        }

        // 获取结果
        $response_list = [];
        foreach ($request_list as $namespaceName => $req) {
            $response_list[$namespaceName] = true;
            $result = curl_multi_getcontent($req['ch']);
            $code = curl_getinfo($req['ch'], CURLINFO_HTTP_CODE);
            $error = curl_error($req['ch']);
            curl_multi_remove_handle($multi_ch, $req['ch']);
            curl_close($req['ch']);
            if ($code == 200) {
                $result = json_decode($result, true);
                $content = '<?php return ' . var_export($result, true) . ';';
                file_put_contents($req['config_file'], $content);
            } elseif ($code != 304) {
                echo 'pull config of namespace[' . $namespaceName . '] error:' . ($result ?: $error) . "\n";
                $response_list[$namespaceName] = false;
            }
        }
        curl_multi_close($multi_ch);
        return $response_list;
    }

    /**
     * @param $ch
     * @param null $callback
     * @throws Exception
     */
    protected function _listenChange($ch, $callback = null): void
    {
        $base_url = rtrim($this->configServer, '/') . '/notifications/v2?';
        $params = [];
        $params['appId'] = $this->appId;
        $params['cluster'] = $this->cluster;
        do {
            $params['notifications'] = json_encode(array_values($this->notifications));
            $query = http_build_query($params);
            if (empty($this->secret)) {
                curl_setopt($ch, CURLOPT_HEADER, false);
            } else {
                $this->timestamp = $this->getMillisecond();
                $header = $this->getHeader($base_url . $query);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt($ch, CURLOPT_URL, $base_url . $query);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            if ($httpCode == 200) {
                $res = json_decode($response, true);
                $change_list = [];
                foreach ($res as $r) {
                    if ($r['notificationId'] != $this->notifications[$r['namespaceName']]['notificationId']) {
                        $change_list[$r['namespaceName']] = $r['notificationId'];
                    }
                }
                $response_list = $this->pullConfigBatch(array_keys($change_list));
                foreach ($response_list as $namespaceName => $result) {
                    $result && ($this->notifications[$namespaceName]['notificationId'] = $change_list[$namespaceName]);
                }
                //如果定义了配置变更的回调，比如重新整合配置，则执行回调
                ($callback instanceof Closure) && call_user_func($callback);
            } elseif ($httpCode != 304) {
                throw new Exception($response ?: $error);
            }
        } while (true);
    }

    /**
     * 监听到配置变更时的回调处理
     * @param null $callback
     * @return string|null
     * @throws Exception
     */
    public function start($callback = null): ?string
    {
        $ch = curl_init();
        try {
            $this->init();
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->intervalTimeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $this->_listenChange($ch, $callback);
            return null;
        } catch (Exception $e) {
            curl_close($ch);
            return $e->getMessage();
        }
    }

    /**
     * 获取13位时间戳
     * @return int
     */
    private function getMillisecond(): int
    {
        list($t1, $t2) = explode(' ', microtime());
        return (int)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}