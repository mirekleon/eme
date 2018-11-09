<?php

namespace Eme\Core\Http;

use Eme\Core\Http\Files;
use Eme\Core\Http\Server;
use Eme\Core\Util\Parameter;

/**
 *
 */
class Request
{
    /**
     *
     */
    protected $trustedProxies = [];
    /**
     *
     */
    const METHOD_OPTIONS  = 'OPTIONS';
    const METHOD_GET      = 'GET';
    const METHOD_HEAD     = 'HEAD';
    const METHOD_POST     = 'POST';
    const METHOD_PUT      = 'PUT';
    const METHOD_DELETE   = 'DELETE';
    const METHOD_TRACE    = 'TRACE';
    const METHOD_CONNECT  = 'CONNECT';
    const METHOD_PATCH    = 'PATCH';
    /**
     *
     */
    protected $get = [];
    /**
     *
     */
    protected $post = [];
    /**
     *
     */
    protected $cookies = [];
    /**
     *
     */
    protected $files = [];
    /**
     *
     */
    public $server = [];
    /**
     *
     */
    protected $method = self::METHOD_GET;
    /**
     *
     */
    protected $baseUrl;
    /**
     *
     */
    protected $requestUri;
    /**
     *
     */
    public function __construct(
        array $get = [],
        array $post = [],
        array $cookies = [],
        array $files = [],
        array $server = []
    ) {
        $this->init($get, $post, $cookies, $files, $server);
    }
    /**
     *
     */
    protected function init($get, $post, $cookies, $files, $server)
    {
        $this->setTrustedProxies(['127.0.0.1', 'localhost', 'localhost.localdomain']);

        $this->get = new Parameter($get);
        $this->post = new Parameter($post);
        $this->cookies = new Parameter($cookies);
        $this->files = new Files($files);
        $this->server = new Server($server);
    }
    /**
     *
     */
    public static function create()
    {
        return new static(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }
    /*
     * The following method is from code of the Zend Framework
     * Code subject to the new BSD license (http://framework.zend.com/license/new-bsd).
     * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     */
    protected function detectUri()
    {
        $requestUri = null;
        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $this->server->get('IIS_WasUrlRewritten');
        $unencodedUrl    = $this->server->get('UNENCODED_URL', '');
        if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
            return $unencodedUrl;
        }
        $requestUri = $this->server->get('REQUEST_URI');
        // HTTP proxy requests setup request URI with scheme and host [and port]
        // + the URL path, only use URL path.
        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }
        // IIS 5.0, PHP as CGI.
        $origPathInfo = $this->server->get('ORIG_PATH_INFO');
        if ($origPathInfo !== null) {
            $queryString = $this->server->get('QUERY_STRING', '');
            if ($queryString !== '') {
                $origPathInfo .= '?' . $queryString;
            }
            return $origPathInfo;
        }
        return '/';
    }
    /**
     *
     */
    public function getRequestUri()
    {
        return (null === $this->requestUri) ? $this->detectUri() : $this->requestUri;
    }
    /**
     *
     */
    public function getSchemeAndHttpHost()
    {
        return $this->getScheme().'://'.$this->getHttpHost();
    }
    /**
     *
     */
    public function setTrustedProxies(array $trustedProxies = [], $append = false)
    {
        if (empty($trustedProxies)) {
            return;
        }

        if (true === $append) {
            $this->trustedProxies = array_unique(array_merge($this->trustedProxies, $trustedProxies));
            $this->trustedProxies = array_values($this->trustedProxies);
        } else {
            $this->trustedProxies = $trustedProxies;
        }
    }
    /**
     *
     */
    public function getTrustedProxies()
    {
        return $this->trustedProxies;
    }
    /**
     *
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }
    /**
     *
     */    
    public function isSecure()
    {
        $https = $this->server->get('HTTPS');
        $proto = $this->server->get('HTTP_X_FORWARDED_PROTO');
        $ssl = $this->server->get('HTTP_X_FORWARDED_SSL');
        if (!empty($https) && $https == 'on') {
            return true;
        }
        if (!empty($proto) && $proto == 'https' || !empty($ssl) && $ssl == 'on') {
            return true;
        }
        return false;
    }
    /**
     *
     */    
    public function getHost()
    {
        return $this->server->get('HTTP_HOST');
    }
    /**
     *
     */    
    public function getMethod()
    {
        return $this->method;
    }
    /**
     *
     */
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->server->get('SERVER_PORT');
        if (('http' == $scheme && 80 == $port) || ('https' == $scheme && 443 == $port)) {
            return $this->getHost();
        }
        return $this->getHost().':'.$port;
    }
}
