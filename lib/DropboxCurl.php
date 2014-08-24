<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DropboxCurl class minimal wrapper around a cURL handle
 *
 * PHP version 5
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  FileSystem
 * @package   DropboxFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/borislav-angelov/dropbox-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.0.0
 * @link      https://github.com/borislav-angelov/dropbox-factory/
 */

/**
 * DropboxCurl class
 *
 * @category  FileSystem
 * @package   DropboxFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/borislav-angelov/dropbox-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.0.0
 * @link      https://github.com/borislav-angelov/dropbox-factory/
 */

class DropboxCurl
{
    protected $baseURL = null;

    protected $path    = null;

    protected $handler = null;

    protected $options = array();

    public function __construct($baseURL, $accessToken = null, $userAgent = 'AI1WM') {
        // Check the cURL extension is loaded
        if (!extension_loaded('curl')) {
            throw new Exception('Dropbox Factory requires cURL extension');
        }

        // Set base URL
        $this->baseURL = $baseURL;

        // Default configuration
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_CONNECTTIMEOUT, 10);
        $this->setOption(CURLOPT_LOW_SPEED_LIMIT, 1024);
        $this->setOption(CURLOPT_LOW_SPEED_TIME, 10);

        // Enable SSL support
        $this->setOption(CURLOPT_SSL_VERIFYPEER, true);
        $this->setOption(CURLOPT_SSL_VERIFYHOST, 2);
        $this->setOption(CURLOPT_SSLVERSION, 3);
        $this->setOption(CURLOPT_CAINFO, __DIR__ . '/../certs/trusted-certs.crt');
        $this->setOption(CURLOPT_CAPATH, __DIR__ . '/../certs/');

        $headers = array();

        // Add Access Token
        if ($accessToken) {
            $headers[] = "Authorization: Bearer $accessToken";
        }

        // Add User Agent
        if ($userAgent) {
            $headers[] = "User-Agent: $userAgent";
        }

        // Set headers
        $this->setOption(CURLOPT_HTTPHEADER, $headers);

        // Limit vulnerability surface area.  Supported in cURL 7.19.4+
        if (defined('CURLOPT_PROTOCOLS')) {
            $this->setOption(CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        }

        if (defined('CURLOPT_REDIR_PROTOCOLS')) {
            $this->setOption(CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
        }
    }

    /**
     * Set cURL path
     *
     * @param  string      $value Resouse path
     * @return DropboxCurl
     */
    public function setPath($value) {
        $this->path = $value;
        return $this;
    }

    /**
     * Get cURL path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set cURL option
     *
     * @param  int         $option cURL option name
     * @param  mixed       $value  cURL option value
     * @return DropboxCurl
     */
    public function setOption($name, $value) {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * Get cURL option
     *
     * @param  int   $option
     * @return mixed
     */
    public function getOption($name) {
        return $this->options[$name];
    }

    /**
     * Make cURL request
     *
     * @return array
     */
    public function makeRequest() {
        $this->handler = curl_init($this->baseURL . $this->getPath());

        // Apply cURL options
        foreach ($this->options as $name => $value) {
            curl_setopt($this->handler, $name, $value);
        }

        // HTTP request
        $body = curl_exec($this->handler);
        $code = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
        if ($body === false || $status !== 200) {
            throw new Exception('Error executing HTTP request: ' . curl_error($this->handler));
        }

        return json_decode($body, true);
    }

    /**
     * Destroy cURL handler
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->handler !== null) {
            curl_close($this->handler);
        }
    }
}
