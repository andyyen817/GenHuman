/**
 * Gemini AI API 集成服务
 * 用于AI编剧功能的大模型调用
 */

// Gemini API配置
const GEMINI_API_KEY = 'AIzaSyBWJBYemDS3KUblQS6wymW0BDuQqSNYX2o'
const GEMINI_API_BASE = 'https://generativelanguage.googleapis.com/v1beta'
const MODEL_NAME = 'gemini-2.0-flash-exp' // 使用2.5 Flash模型

/**
 * 调用Gemini API生成内容
 * @param {string} prompt - 提示词
 * @param {Object} options - 配置选项
 * @returns {Promise<string>} 生成的内容
 */
export async function generateContent(prompt, options = {}) {
  const {
    temperature = 0.7,
    maxTokens = 2048,
    systemInstruction = ''
  } = options

  try {
    console.log(`[${new Date().toLocaleTimeString()}] 🚀 开始调用Gemini API`)
    console.log(`[${new Date().toLocaleTimeString()}] 📊 提示词长度: ${prompt.length}字符`)
    
    const requestBody = {
      contents: [
        {
          parts: [
            {
              text: systemInstruction ? `${systemInstruction}\n\n${prompt}` : prompt
            }
          ]
        }
      ],
      generationConfig: {
        temperature: temperature,
        maxOutputTokens: maxTokens,
        topP: 0.95,
        topK: 64
      },
      safetySettings: [
        {
          category: "HARM_CATEGORY_HARASSMENT",
          threshold: "BLOCK_MEDIUM_AND_ABOVE"
        },
        {
          category: "HARM_CATEGORY_HATE_SPEECH", 
          threshold: "BLOCK_MEDIUM_AND_ABOVE"
        },
        {
          category: "HARM_CATEGORY_SEXUALLY_EXPLICIT",
          threshold: "BLOCK_MEDIUM_AND_ABOVE"
        },
        {
          category: "HARM_CATEGORY_DANGEROUS_CONTENT",
          threshold: "BLOCK_MEDIUM_AND_ABOVE"
        }
      ]
    }

    const response = await fetch(
      `${GEMINI_API_BASE}/models/${MODEL_NAME}:generateContent?key=${GEMINI_API_KEY}`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestBody)
      }
    )

    console.log(`[${new Date().toLocaleTimeString()}] 📡 API响应状态: ${response.status}`)

    if (!response.ok) {
      const errorData = await response.json()
      console.error(`[${new Date().toLocaleTimeString()}] ❌ API调用失败:`, errorData)
      throw new Error(`Gemini API错误: ${response.status} - ${errorData.error?.message || '未知错误'}`)
    }

    const data = await response.json()
    console.log(`[${new Date().toLocaleTimeString()}] ✅ API调用成功`)
    
    if (!data.candidates || data.candidates.length === 0) {
      throw new Error('Gemini API返回空结果')
    }

    const generatedText = data.candidates[0].content.parts[0].text
    console.log(`[${new Date().toLocaleTimeString()}] 📊 生成内容长度: ${generatedText.length}字符`)
    
    return generatedText

  } catch (error) {
    console.error(`[${new Date().toLocaleTimeString()}] ❌ Gemini API调用失败:`, error)
    throw new Error(`AI生成失败: ${error.message}`)
  }
}

/**
 * 生成视频脚本
 * @param {string} userInput - 用户输入的创作需求
 * @param {string} scenario - 创作场景 (scenario1-6)
 * @returns {Promise<Object>} 生成的脚本结构
 */
export async function generateVideoScript(userInput, scenario = 'scenario1') {
  console.log(`[${new Date().toLocaleTimeString()}] 🎬 开始生成视频脚本`)
  console.log(`[${new Date().toLocaleTimeString()}] 📋 场景类型: ${scenario}`)
  console.log(`[${new Date().toLocaleTimeString()}] 💭 用户输入: ${userInput}`)

  // 根据场景选择不同的提示词
  const promptTemplates = {
    scenario1: `你是专业的短视频编剧Tony。用户想要从零创作一个视频，请根据用户的想法生成一个完整的视频脚本。

用户需求：${userInput}

请生成一个5幕的短视频脚本，每幕8-12秒，总时长约50秒。脚本要求：
1. 内容要吸引人，适合短视频平台
2. 语言要简洁明了，口语化
3. 要有明确的价值输出
4. 结构要完整：开头吸引-内容展开-结尾call to action

请按以下JSON格式返回：
{
  "title": "视频标题",
  "theme": "主题描述", 
  "target_audience": "目标观众",
  "total_duration": 50,
  "acts": [
    {
      "act_number": 1,
      "content": "第一幕的具体文案内容",
      "duration": 8,
      "scene_description": "画面描述",
      "key_points": ["要点1", "要点2"]
    },
    // ... 其他4幕
  ]
}`,

    scenario4: `你是专业的短视频编剧Tony。用户有现成的文案，需要你帮助分镜到数字人视频中。

用户文案：${userInput}

请将这段文案合理分配到5个分镜中，每个分镜8-12秒。要求：
1. 保持原文案的完整性和逻辑性
2. 每个分镜的内容要相对独立
3. 适合数字人口播的节奏
4. 添加合适的画面描述

请按以下JSON格式返回：
{
  "title": "根据文案内容生成的标题",
  "original_content": "${userInput}",
  "total_duration": 50,
  "acts": [
    {
      "act_number": 1,
      "content": "分配到第一幕的文案内容",
      "duration": 10,
      "scene_description": "建议的画面或背景描述",
      "key_points": ["这一幕的要点"]
    },
    // ... 其他4幕
  ]
}`
  }

  const prompt = promptTemplates[scenario] || promptTemplates.scenario1

  try {
    const generatedContent = await generateContent(prompt, {
      temperature: 0.8,
      maxTokens: 2048,
      systemInstruction: '你是Vidspark AI专属编剧Tony，专门创作吸引人的短视频脚本。请确保返回标准的JSON格式，不要包含任何其他文字。'
    })

    // 清理生成的内容，确保是有效的JSON
    let cleanedContent = generatedContent.trim()
    
    // 移除可能的markdown代码块标记
    if (cleanedContent.startsWith('```json')) {
      cleanedContent = cleanedContent.replace(/^```json\s*/, '').replace(/\s*```$/, '')
    } else if (cleanedContent.startsWith('```')) {
      cleanedContent = cleanedContent.replace(/^```\s*/, '').replace(/\s*```$/, '')
    }

    console.log(`[${new Date().toLocaleTimeString()}] 🧹 清理后的内容:`, cleanedContent.substring(0, 200) + '...')

    const scriptData = JSON.parse(cleanedContent)
    
    console.log(`[${new Date().toLocaleTimeString()}] ✅ 脚本生成成功`)
    console.log(`[${new Date().toLocaleTimeString()}] 📊 标题: ${scriptData.title}`)
    console.log(`[${new Date().toLocaleTimeString()}] 📊 分幕数: ${scriptData.acts?.length || 0}`)

    return scriptData

  } catch (error) {
    console.error(`[${new Date().toLocaleTimeString()}] ❌ 脚本生成失败:`, error)
    
    // 如果JSON解析失败，返回一个默认的脚本结构
    return {
      title: "AI生成的视频脚本",
      theme: "基于用户需求创作",
      target_audience: "通用观众",
      total_duration: 50,
      error: error.message,
      acts: [
        {
          act_number: 1,
          content: "大家好，今天我要和大家分享一个很有用的内容",
          duration: 10,
          scene_description: "开场画面，主持人出现",
          key_points: ["开场问候", "引出主题"]
        },
        {
          act_number: 2,
          content: userInput || "这是用户想要分享的核心内容",
          duration: 12,
          scene_description: "核心内容展示",
          key_points: ["核心要点"]
        },
        {
          act_number: 3,
          content: "让我来详细解释一下这个方法的具体步骤",
          duration: 10,
          scene_description: "详细说明画面",
          key_points: ["具体步骤"]
        },
        {
          act_number: 4,
          content: "这样做的好处是显而易见的，效果非常明显",
          duration: 8,
          scene_description: "效果展示",
          key_points: ["效果说明"]
        },
        {
          act_number: 5,
          content: "如果你觉得有用，请点赞关注，我们下期再见",
          duration: 10,
          scene_description: "结尾画面，call to action",
          key_points: ["互动引导", "结束语"]
        }
      ]
    }
  }
}

/**
 * 生成视频标题建议
 * @param {string} topic - 视频主题
 * @returns {Promise<Array>} 标题建议数组
 */
export async function generateTitleSuggestions(topic) {
  console.log(`[${new Date().toLocaleTimeString()}] 💡 开始生成标题建议`)
  
  const prompt = `你是短视频标题专家，请为以下主题生成10个吸引人的短视频标题：

主题：${topic}

要求：
1. 标题要吸引眼球，激发观看欲望
2. 长度控制在15-25字之间
3. 适合抖音、小红书等短视频平台
4. 包含热门关键词和数字
5. 语言要口语化，贴近年轻人

请按以下格式返回：
1. 标题一
2. 标题二
...
10. 标题十`

  try {
    const generatedContent = await generateContent(prompt, {
      temperature: 0.9,
      maxTokens: 1024
    })

    // 解析生成的标题列表
    const titles = generatedContent
      .split('\n')
      .filter(line => /^\d+\./.test(line.trim()))
      .map(line => line.replace(/^\d+\.\s*/, '').trim())
      .filter(title => title.length > 0)

    console.log(`[${new Date().toLocaleTimeString()}] ✅ 生成了${titles.length}个标题建议`)
    
    return titles

  } catch (error) {
    console.error(`[${new Date().toLocaleTimeString()}] ❌ 标题生成失败:`, error)
    return [
      `${topic}的终极指南`,
      `你不知道的${topic}小技巧`,
      `3分钟学会${topic}`,
      `${topic}新手必看攻略`,
      `关于${topic}的5个秘密`
    ]
  }
}

/**
 * 测试Gemini API连接
 * @returns {Promise<boolean>} 连接是否成功
 */
export async function testGeminiConnection() {
  console.log(`[${new Date().toLocaleTimeString()}] 🔧 开始测试Gemini API连接`)
  
  try {
    const result = await generateContent('请回复"连接成功"', {
      temperature: 0,
      maxTokens: 100
    })
    
    console.log(`[${new Date().toLocaleTimeString()}] ✅ Gemini API连接测试成功`)
    console.log(`[${new Date().toLocaleTimeString()}] 📋 响应内容:`, result)
    
    return true
    
  } catch (error) {
    console.error(`[${new Date().toLocaleTimeString()}] ❌ Gemini API连接测试失败:`, error)
    return false
  }
}
