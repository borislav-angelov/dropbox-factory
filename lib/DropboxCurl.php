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
    protected $handler = null;

    public function __construct($url, $accessToken = null, $userAgent = 'AI1WM') {
        // Check the cURL extension is loaded
        if (!extension_loaded('curl')) {
            throw new Exception('Dropbox Factory requires cURL extension');
        }

        $this->handler = curl_init($url);

        // Default configuration
        $this->set(CURLOPT_RETURNTRANSFER, true);
        $this->set(CURLOPT_CONNECTTIMEOUT, 10);
        $this->set(CURLOPT_LOW_SPEED_LIMIT, 1024);
        $this->set(CURLOPT_LOW_SPEED_TIME, 10);

        // Enable SSL support
        $this->set(CURLOPT_SSL_VERIFYPEER, true);
        $this->set(CURLOPT_SSL_VERIFYHOST, 2);
        $this->set(CURLOPT_SSLVERSION, 3);
        $this->set(CURLOPT_CAINFO, __DIR__ . '/../certs/trusted-certs.crt');
        $this->set(CURLOPT_CAPATH, __DIR__ . '/../certs/');

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
        $this->set(CURLOPT_HTTPHEADER, $headers);

        // Limit vulnerability surface area.  Supported in cURL 7.19.4+
        if (defined('CURLOPT_PROTOCOLS')) {
            $this->set(CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        }

        if (defined('CURLOPT_REDIR_PROTOCOLS')) {
            $this->set(CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
        }
    }

    /**
     * Set cURL option
     *
     * @param int $option
     * @param mixed $value
     * @return void
     */
    public function set($option, $value) {
        curl_setopt($this->handler, $option, $value);
    }

    /**
     * Execute cURL request
     *
     * @return array
     */
    public function exec() {
        $body = curl_exec($this->handler);
        if ($body === false) {
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
        curl_close($this->handler);
    }
}
