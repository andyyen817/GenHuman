<?php

namespace app\service;

/**
 * Vidspark 多語言管理服務
 * 遵循 genhuman開發規則.md - 簡化邏輯原則
 */
class I18nService
{
    private static $dictionaryPath = '';
    
    public function __construct()
    {
        // 初始化字典文件路徑
        self::$dictionaryPath = base_path() . '/storage/i18n/dictionary.json';
        
        // 確保存儲目錄存在
        $this->ensureStorageDirectory();
    }
    
    /**
     * 確保存儲目錄存在
     */
    private function ensureStorageDirectory()
    {
        $dir = dirname(self::$dictionaryPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // 如果字典文件不存在，創建空字典
        if (!file_exists(self::$dictionaryPath)) {
            $this->saveDictionary([]);
        }
    }
    
    /**
     * 獲取完整語言字典
     * @return array
     */
    public function getDictionary()
    {
        if (!file_exists(self::$dictionaryPath)) {
            return [];
        }
        
        $content = file_get_contents(self::$dictionaryPath);
        return json_decode($content, true) ?: [];
    }
    
    /**
     * 保存語言字典
     * @param array $dictionary
     * @return bool
     */
    private function saveDictionary(array $dictionary)
    {
        return file_put_contents(
            self::$dictionaryPath, 
            json_encode($dictionary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ) !== false;
    }
    
    /**
     * 獲取特定字段的翻譯
     * @param string $key 英文字段key
     * @return array|null
     */
    public function getTranslation($key)
    {
        $dictionary = $this->getDictionary();
        return $dictionary[$key] ?? null;
    }
    
    /**
     * 新增或更新翻譯字段
     * @param string $key 英文字段key
     * @param array $translations 語言翻譯 ["en" => "...", "zh-TW" => "...", "zh-CN" => "..."]
     * @return bool
     */
    public function setTranslation($key, array $translations)
    {
        $dictionary = $this->getDictionary();
        $dictionary[$key] = $translations;
        return $this->saveDictionary($dictionary);
    }
    
    /**
     * 刪除翻譯字段
     * @param string $key 英文字段key
     * @return bool
     */
    public function deleteTranslation($key)
    {
        $dictionary = $this->getDictionary();
        if (isset($dictionary[$key])) {
            unset($dictionary[$key]);
            return $this->saveDictionary($dictionary);
        }
        return true;
    }
    
    /**
     * 批量更新翻譯（用於Excel導入）
     * @param array $translations
     * @return array 操作結果
     */
    public function batchUpdate(array $translations)
    {
        $dictionary = $this->getDictionary();
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($translations as $key => $translation) {
            // 驗證字段key格式（駝峰命名）
            if (!$this->validateKey($key)) {
                $results['failed']++;
                $results['errors'][] = "Invalid key format: {$key}";
                continue;
            }
            
            $dictionary[$key] = $translation;
            $results['success']++;
        }
        
        if ($this->saveDictionary($dictionary)) {
            return $results;
        } else {
            return ['success' => 0, 'failed' => count($translations), 'errors' => ['Failed to save dictionary']];
        }
    }
    
    /**
     * 驗證字段key格式（駝峰命名法）
     * @param string $key
     * @return bool
     */
    private function validateKey($key)
    {
        // 檢查是否為有效的駝峰命名
        return preg_match('/^[a-z][a-zA-Z0-9_]*$/', $key);
    }
    
    /**
     * 獲取所有支援的語言列表
     * @return array
     */
    public function getSupportedLanguages()
    {
        return [
            'en' => 'English',
            'zh-TW' => '繁體中文',
            'zh-CN' => '简体中文'
        ];
    }
    
    /**
     * 獲取字典統計信息
     * @return array
     */
    public function getStatistics()
    {
        $dictionary = $this->getDictionary();
        $languages = $this->getSupportedLanguages();
        
        $stats = [
            'total_keys' => count($dictionary),
            'languages' => []
        ];
        
        foreach ($languages as $code => $name) {
            $translated = 0;
            foreach ($dictionary as $translations) {
                if (!empty($translations[$code])) {
                    $translated++;
                }
            }
            $stats['languages'][$code] = [
                'name' => $name,
                'translated' => $translated,
                'percentage' => count($dictionary) > 0 ? round(($translated / count($dictionary)) * 100, 1) : 0
            ];
        }
        
        return $stats;
    }
}


