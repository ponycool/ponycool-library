<?php
/**
 * Created by PhpStorm
 * User: Pony
 * Date: 2021/4/13
 * Time: 3:31 下午
 */

declare(strict_types=1);

namespace PonyCool\Cors;

class Handler
{
    private Header $headers;
    private array $options;

    public function __construct(array $options)
    {
        $this->headers = new Header();
        $this->options = $this->normalizeOptions($options);
    }

    /**
     * 标准化参数
     * @param array $options
     * @return array
     */
    private function normalizeOptions(array $options): array
    {
        $options += [
            'allowedOrigins' => [],
            'allowedOriginsPatterns' => [],
            // 表示是否允许发送Cookie
            'supportsCredentials' => false,
            'allowedHeaders' => [],
            'exposedHeaders' => [],
            'allowedMethods' => [],
            // 预检请求的有效期，单位为秒
            'maxAge' => 0,
        ];

        // normalize array('*') to true
        if (in_array('*', $options['allowedOrigins'])) {
            $options['allowedOrigins'] = true;
        }
        if (in_array('*', $options['allowedHeaders'])) {
            $options['allowedHeaders'] = true;
        } else {
            $options['allowedHeaders'] = array_map('strtolower', $options['allowedHeaders']);
        }

        if (in_array('*', $options['allowedMethods'])) {
            $options['allowedMethods'] = true;
        } else {
            $options['allowedMethods'] = array_map('strtoupper', $options['allowedMethods']);
        }

        return $options;
    }

    /**
     * 是否是授权的跨域请求
     * @return bool
     */
    public function isActualRequestAllowed(): bool
    {
        return $this->isOriginAllowed();
    }

    /**
     * 源是否授权
     * @return bool
     */
    public function isOriginAllowed(): bool
    {
        if ($this->options['allowedOrigins'] === true) {
            return true;
        }
        if (!$this->headers->has('Origin')) {
            return false;
        }

        $origin = $this->headers->get('Origin');
        if (in_array($origin, $this->options['allowedOrigins'])) {
            return true;
        }

        foreach ($this->options['allowedOriginsPatterns'] as $pattern) {
            if (preg_match($pattern, $origin)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 是否是跨域请求
     * @return bool
     */
    public function isCorsRequest(): bool
    {
        return $this->headers->has('Origin');
    }

    /**
     * 是否是预请求
     * @return bool
     */
    public function isPreflightRequest(): bool
    {
        return $this->getMethod() === 'OPTIONS' && $this->headers->has('Access-Control-Request-Method');
    }

    /**
     * 获取大写的请求方法
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }


    /**
     * 处理跨域预请求
     */
    public function handlePreflightRequest(): void
    {
        $this->addPreflightRequestHeaders();
        $this->varyHeader("Access-Control-Request-Method");
        exit('OK');
    }

    /**
     * 增加预请求header头
     */
    public function addPreflightRequestHeaders(): void
    {
        $this->configureAllowedOrigin();

        if ($this->headers->has('Access-Control-Allow-Origin')) {
            $this->configureAllowCredentials();

            $this->configureAllowedMethods();

            $this->configureAllowedHeaders();

            $this->configureMaxAge();
        }

    }

    /**
     * 增加实际请求header头
     */
    public function addActualRequestHeaders(): void
    {
        $this->configureAllowedOrigin();

        if ($this->headers->has('Access-Control-Allow-Origin')) {
            $this->configureAllowCredentials();

            $this->configureExposedHeaders();
        }
    }

    /**
     * vary处理
     * @param $header
     */
    public function varyHeader($header): void
    {
        $responseHeaders = headers_list();
        if (!array_key_exists('Vary', $responseHeaders)) {
            $this->setHeader('Vary', $header);
        } elseif (!in_array($header, explode(', ', $responseHeaders['Vary']))) {
            $this->setHeader('Vary', $responseHeaders['Vary'] . ', ' . $header);
        }
    }

    /**
     * 设置header
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value): void
    {
        header(sprintf('%s: %s', $key, $value));
    }

    /**
     * 是否是同源请求
     * @return bool
     */
    public function isSameHost(): bool
    {
        $schemeAndHttpHost = sprintf(
            "%s://%s",
            $_SERVER['REQUEST_SCHEME'],
            $_SERVER['SERVER_NAME']
        );
        $port = $_SERVER['SERVER_PORT'];
        if ($port !== "80") {
            $schemeAndHttpHost = sprintf("%s:%s", $schemeAndHttpHost, $port);
        }
        return $this->headers->get('Origin') === $schemeAndHttpHost;
    }

    /**
     * 配置授权源
     */
    private function configureAllowedOrigin(): void
    {
        if ($this->options['allowedOrigins'] === true && !$this->options['supportsCredentials']) {
            // Safe+cacheable, allow everything
            $this->setHeader('Access-Control-Allow-Origin', '*');
        } elseif ($this->isSingleOriginAllowed()) {
            // Single origins can be safely set
            $this->setHeader('Access-Control-Allow-Origin', array_values($this->options['allowedOrigins'])[0]);
        } else {
            // For dynamic headers, set the requested Origin header when set and allowed
            if ($this->isCorsRequest() && $this->isOriginAllowed()) {
                $this->setHeader('Access-Control-Allow-Origin', $this->headers->get('Origin'));
            }
            $this->varyHeader('Origin');
        }
    }

    /**
     * 是否是单个源授权
     * @return bool
     */
    private function isSingleOriginAllowed(): bool
    {
        if ($this->options['allowedOrigins'] === true || !empty($this->options['allowedOriginsPatterns'])) {
            return false;
        }

        return count($this->options['allowedOrigins']) === 1;
    }

    /**
     * 配置允许发送Cookie
     */
    private function configureAllowCredentials(): void
    {
        if ($this->options['supportsCredentials']) {
            $this->setHeader('Access-Control-Allow-Credentials', 'true');
        }
    }

    /**
     * 配置合法的请求方法
     */
    private function configureAllowedMethods(): void
    {
        if ($this->options['allowedMethods'] === true) {
            $allowMethods = strtoupper($this->headers->get('Access-Control-Request-Method'));
            $this->varyHeader('Access-Control-Request-Method');
        } else {
            $allowMethods = implode(', ', $this->options['allowedMethods']);
        }

        $this->setHeader('Access-Control-Allow-Methods', $allowMethods);
    }

    /**
     * 配置Header
     */
    private function configureAllowedHeaders(): void
    {
        if ($this->options['allowedHeaders'] === true) {
            $allowHeaders = $this->headers->get('Access-Control-Request-Headers');
            $this->varyHeader('Access-Control-Request-Headers');
        } else {
            $allowHeaders = implode(', ', $this->options['allowedHeaders']);
        }
        $this->setHeader('Access-Control-Allow-Headers', $allowHeaders);
    }

    /**
     * 配置额外暴露的字段
     */
    private function configureExposedHeaders(): void
    {
        if ($this->options['exposedHeaders']) {
            $this->setHeader('Access-Control-Expose-Headers', implode(', ', $this->options['exposedHeaders']));
        }
    }

    /**
     * 设置预检请求的有效期，单位为秒
     */
    private function configureMaxAge(): void
    {
        if ($this->options['maxAge'] !== null) {
            $this->setHeader('Access-Control-Max-Age', (int)$this->options['maxAge']);
        }
    }

}