<?php
namespace lib\Storage;

use GuzzleHttp\Client;

class WebDav {
    private $client;
    private $baseUri;
    private $errmsg;
    private $filepath = 'file/';

    // 构造函数：初始化 WebDAV 连接（需配置地址、账号、密码）
    function __construct($baseUri, $username, $password) {
        $this->baseUri = rtrim($baseUri, '/') . '/';
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'auth' => [$username, $password],
            'headers' => ['Depth' => 1] // WebDAV 协议需要的深度头
        ]);
    }

    // 检查文件是否存在（通过 PROPFIND 请求）
    function exists($name) {
        try {
            $response = $this->client->request('PROPFIND', $this->filepath . $name);
            return $response->getStatusCode() === 207; // WebDAV 成功状态码
        } catch (\Exception $e) {
            $this->errmsg = $e->getMessage();
            return false;
        }
    }

    // 下载文件（通过 GET 请求）
    function downfile($name, $size = 0) {
        try {
            $response = $this->client->get($this->filepath . $name);
            echo $response->getBody();
            return true;
        } catch (\Exception $e) {
            $this->errmsg = $e->getMessage();
            return false;
        }
    }

    // 上传文件（通过 PUT 请求）
    function upload($name, $tmpfile) {
        try {
            $this->client->put($this->filepath . $name, [
                'body' => fopen($tmpfile, 'rb')
            ]);
            return true;
        } catch (\Exception $e) {
            $this->errmsg = $e->getMessage();
            return false;
        }
    }

    // 删除文件（通过 DELETE 请求）
    function delete($name) {
        try {
            $this->client->delete($this->filepath . $name);
            return true;
        } catch (\Exception $e) {
            $this->errmsg = $e->getMessage();
            return false;
        }
    }

    // 其他必要方法（getinfo、get 等）
    function getinfo($name) {
        // 通过 PROPFIND 获取文件大小、类型等元数据
        // 实现略...
    }

    function errmsg() {
        return $this->errmsg;
    }
}