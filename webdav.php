<?php
/**
 * WebDAV 存储适配器
 * 解决 403 Forbidden、路径不存在、权限不足等问题
 */
namespace lib\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;

class WebDav {
    private $client; // Guzzle 客户端实例
    private $webdavUri; // WebDAV 根地址
    private $user; // WebDAV 用户名
    private $pwd; // WebDAV 密码
    private $errmsg = ''; // 错误信息
    private $filePath = 'file/'; // 文件存储子路径（可配置）

    /**
     * 构造函数：初始化 WebDAV 客户端
     * @param string $uri WebDAV 服务地址（如 https://pan.moe/dav/）
     * @param string $user 用户名
     * @param string $pwd 密码
     */
    public function __construct($uri, $user, $pwd) {
        $this->webdavUri = rtrim($uri, '/');
        $this->user = $user;
        $this->pwd = $pwd;

        // 初始化 Guzzle 客户端，解决认证、请求头、超时等问题
        $this->client = new Client([
            'auth' => [$this->user, $this->pwd], // Basic Auth 认证（核心）
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)',
                'Accept' => '*/*',
                'Content-Type' => 'application/octet-stream',
                'Connection' => 'keep-alive'
            ],
            'timeout' => 300, // 大文件上传超时（5分钟）
            'connect_timeout' => 30, // 连接超时
            'verify' => false, // 忽略 SSL 证书错误（生产环境建议改为证书路径）
            'http_errors' => true // 抛出 HTTP 错误状态码异常
        ]);

        // 初始化时自动创建存储文件夹（避免路径不存在导致403）
        $this->createFolder($this->filePath);
    }

    /**
     * 自动创建文件夹（解决 403 路径不存在问题）
     * @param string $path 文件夹路径（相对根地址）
     * @return bool 创建结果
     */
    private function createFolder($path) {
        $folderUrl = $this->webdavUri . '/' . ltrim($path, '/');
        try {
            // MKCOL 是 WebDAV 创建文件夹的标准方法
            $response = $this->client->request('MKCOL', $folderUrl);
            $this->errmsg = "文件夹创建成功：{$path}";
            return true;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            // 405 Method Not Allowed = 文件夹已存在，无需处理
            if ($statusCode == 405) {
                return true;
            }
            $this->errmsg = "创建文件夹失败（{$path}）：{$statusCode} - {$e->getMessage()}";
            return false;
        } catch (ConnectException $e) {
            $this->errmsg = "连接 WebDAV 服务器失败：{$e->getMessage()}";
            return false;
        } catch (\Exception $e) {
            $this->errmsg = "创建文件夹异常：{$e->getMessage()}";
            return false;
        }
    }

    /**
     * 上传文件（解决 403 权限不足问题）
     * @param string $hash 文件哈希名（唯一标识）
     * @param string $tmpName 本地临时文件路径
     * @return bool 上传结果
     */
    public function upload($hash, $tmpName) {
        if (!file_exists($tmpName) || !is_readable($tmpName)) {
            $this->errmsg = "本地临时文件不存在或不可读：{$tmpName}";
            return false;
        }

        $fileUrl = $this->webdavUri . '/' . $this->filePath . $hash;
        try {
            // 以流方式上传（避免大文件内存溢出）
            $response = $this->client->put($fileUrl, [
                'body' => fopen($tmpName, 'r'),
                'headers' => [
                    'Content-Length' => filesize($tmpName) // 携带文件大小头
                ]
            ]);

            $statusCode = $response->getStatusCode();
            // 201 Created = 上传成功，204 No Content = 覆盖成功
            if (in_array($statusCode, [201, 204])) {
                $this->errmsg = "文件上传成功：{$hash}";
                return true;
            } else {
                $this->errmsg = "上传响应异常：状态码 {$statusCode}";
                return false;
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            $this->errmsg = "上传失败（{$hash}）：{$statusCode} Forbidden - {$e->getMessage()}，服务器响应：{$responseBody}";
            return false;
        } catch (ConnectException $e) {
            $this->errmsg = "上传时连接服务器失败：{$e->getMessage()}";
            return false;
        } catch (\Exception $e) {
            $this->errmsg = "上传异常：{$e->getMessage()}";
            return false;
        }
    }

    /**
     * 删除文件
     * @param string $hash 文件哈希名
     * @return bool 删除结果
     */
    public function delete($hash) {
        $fileUrl = $this->webdavUri . '/' . $this->filePath . $hash;
        try {
            $response = $this->client->request('DELETE', $fileUrl);
            $statusCode = $response->getStatusCode();
            if ($statusCode == 204) {
                $this->errmsg = "文件删除成功：{$hash}";
                return true;
            } else {
                $this->errmsg = "删除响应异常：状态码 {$statusCode}";
                return false;
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $this->errmsg = "删除失败（{$hash}）：{$statusCode} - {$e->getMessage()}";
            return false;
        } catch (ConnectException $e) {
            $this->errmsg = "删除时连接服务器失败：{$e->getMessage()}";
            return false;
        } catch (\Exception $e) {
            $this->errmsg = "删除异常：{$e->getMessage()}";
            return false;
        }
    }

    /**
     * 检查文件是否存在
     * @param string $hash 文件哈希名
     * @return bool 存在性结果
     */
    public function exists($hash) {
        $fileUrl = $this->webdavUri . '/' . $this->filePath . $hash;
        try {
            $response = $this->client->request('HEAD', $fileUrl);
            $statusCode = $response->getStatusCode();
            return $statusCode == 200;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                $this->errmsg = "文件不存在：{$hash}";
            } else {
                $this->errmsg = "检查文件存在性失败：{$statusCode} - {$e->getMessage()}";
            }
            return false;
        } catch (ConnectException $e) {
            $this->errmsg = "检查文件时连接服务器失败：{$e->getMessage()}";
            return false;
        } catch (\Exception $e) {
            $this->errmsg = "检查文件异常：{$e->getMessage()}";
            return false;
        }
    }

    /**
     * 下载文件（输出到浏览器）
     * @param string $hash 文件哈希名
     * @param int $size 本地记录的文件大小（用于校验）
     * @return bool 下载结果
     */
    public function downfile($hash, $size = 0) {
        $fileUrl = $this->webdavUri . '/' . $this->filePath . $hash;
        try {
            // 流式输出文件（避免大文件内存溢出）
            $response = $this->client->get($fileUrl, [
                'sink' => STDOUT, // 直接输出到标准输出（浏览器）
                'headers' => [
                    'Range' => isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : '' // 支持断点续传
                ]
            ]);

            // 校验文件大小（可选）
            $serverSize = $response->getHeaderLine('Content-Length');
            if ($size > 0 && $serverSize != $size) {
                $this->errmsg = "文件大小不匹配：本地记录 {$size} 字节，服务器返回 {$serverSize} 字节";
                return false;
            }

            $this->errmsg = "文件下载成功：{$hash}";
            return true;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $this->errmsg = "下载失败（{$hash}）：{$statusCode} - {$e->getMessage()}";
            return false;
        } catch (ConnectException $e) {
            $this->errmsg = "下载时连接服务器失败：{$e->getMessage()}";
            return false;
        } catch (\Exception $e) {
            $this->errmsg = "下载异常：{$e->getMessage()}";
            return false;
        }
    }

    /**
     * 获取错误信息
     * @return string 错误详情
     */
    public function errmsg() {
        return $this->errmsg;
    }

    /**
     * 可选：获取文件的 MIME 类型（用于预览）
     * @param string $hash 文件哈希名
     * @return string MIME 类型
     */
    public function getMimeType($hash) {
        $fileUrl = $this->webdavUri . '/' . $this->filePath . $hash;
        try {
            $response = $this->client->request('HEAD', $fileUrl);
            return $response->getHeaderLine('Content-Type') ?: 'application/octet-stream';
        } catch (\Exception $e) {
            $this->errmsg = "获取MIME类型失败：{$e->getMessage()}";
            return 'application/octet-stream';
        }
    }
}
?>