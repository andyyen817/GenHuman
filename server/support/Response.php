<?php
/**
 * 簡化的 Response 類
 * 不依賴 Webman 框架，直接使用 PHP 原生功能
 */

namespace support;

class Response
{
    private $statusCode;
    private $headers;
    private $body;
    
    public function __construct($statusCode = 200, $headers = [], $body = '')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }
    
    /**
     * 設置狀態碼
     */
    public function withStatus($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    
    /**
     * 設置頭部
     */
    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * 設置響應體
     */
    public function withBody($body)
    {
        $this->body = $body;
        return $this;
    }
    
    /**
     * 獲取狀態碼
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    /**
     * 獲取頭部
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * 獲取響應體
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * 發送響應
     */
    public function send()
    {
        // 設置狀態碼
        http_response_code($this->statusCode);
        
        // 設置頭部
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        
        // 輸出響應體
        echo $this->body;
    }
}