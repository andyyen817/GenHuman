/**
 * 提示词管理系统
 * 统一管理所有AI模型调用的提示词模板
 */

/**
 * 提示词模板类别
 */
export const PROMPT_CATEGORIES = {
  SCRIPTWRITING: 'scriptwriting',    // AI编剧
  IMAGE_GENERATION: 'image_generation', // AI生图
  VOICE_SYNTHESIS: 'voice_synthesis',   // 语音合成
  VIDEO_EDITING: 'video_editing',       // 视频剪辑
  TITLE_GENERATION: 'title_generation', // 标题生成
  OPTIMIZATION: 'optimization'          // 内容优化
}

/**
 * 提示词模板数据库
 */
export const PROMPT_TEMPLATES = {
  // ========== AI编剧提示词 ==========
  [PROMPT_CATEGORIES.SCRIPTWRITING]: {
    // 从零创作影片
    scenario1_basic: {
      name: '从零创作 - 基础模板',
      description: '适用于用户提供简单想法的基础创作',
      template: `你是专业的短视频编剧Tony。用户想要从零创作一个视频，请根据用户的想法生成一个完整的视频脚本。

用户需求：{userInput}

请生成一个{actCount}幕的短视频脚本，每幕{duration}秒，总时长约{totalDuration}秒。脚本要求：
1. 内容要吸引人，适合短视频平台
2. 语言要简洁明了，口语化
3. 要有明确的价值输出
4. 结构要完整：开头吸引-内容展开-结尾call to action

请按以下JSON格式返回：
{
  "title": "视频标题",
  "theme": "主题描述", 
  "target_audience": "目标观众",
  "total_duration": {totalDuration},
  "acts": [
    {
      "act_number": 1,
      "content": "第一幕的具体文案内容",
      "duration": {duration},
      "scene_description": "画面描述",
      "key_points": ["要点1", "要点2"]
    }
  ]
}`,
      variables: ['userInput', 'actCount', 'duration', 'totalDuration'],
      defaults: {
        actCount: 5,
        duration: 10,
        totalDuration: 50
      }
    },

    scenario1_detailed: {
      name: '从零创作 - 详细模板',
      description: '适用于用户提供详细需求的高级创作',
      template: `你是Vidspark AI专属编剧Tony，拥有丰富的短视频创作经验。用户想要从零创作一个视频，请根据详细需求生成专业脚本。

创作需求：{userInput}
目标平台：{platform}
视频风格：{style}
目标时长：{totalDuration}秒

请创作一个{actCount}幕的专业短视频脚本，要求：
1. 开头3秒内必须抓住观众注意力
2. 每幕都要有明确的价值点
3. 语言要符合目标平台的用户习惯
4. 结尾要有强烈的互动引导
5. 整体节奏要紧凑有趣

返回格式保持JSON结构，增加以下字段：
- "hook": "开头钩子句"
- "value_proposition": "核心价值主张"
- "call_to_action": "行动召唤"
- "hashtags": ["相关话题标签"]`,
      variables: ['userInput', 'platform', 'style', 'totalDuration', 'actCount'],
      defaults: {
        platform: '抖音',
        style: '教学类',
        totalDuration: 60,
        actCount: 5
      }
    },

    // 快速制作数字人
    scenario4_digital_human: {
      name: '数字人分镜 - 专用模板',
      description: '专门用于数字人视频的文案分镜',
      template: `你是专业的数字人视频编剧Tony。用户有现成的文案，需要你帮助分镜到数字人视频中。

用户文案：{userInput}
数字人类型：{avatarType}
预期时长：{totalDuration}秒

请将这段文案合理分配到{actCount}个分镜中，每个分镜{duration}秒。要求：
1. 保持原文案的完整性和逻辑性
2. 每个分镜的内容要相对独立，适合数字人口播
3. 考虑数字人的表达特点，语速和停顿要合理
4. 为每个分镜设计合适的背景场景
5. 确保内容流畅，没有突兀的断点

特别注意：
- 数字人语速约150字/分钟
- 每个分镜开头要有自然的过渡
- 重要信息要在分镜的前半部分表达
- 最后一个分镜要有明确的结束语`,
      variables: ['userInput', 'avatarType', 'totalDuration', 'actCount', 'duration'],
      defaults: {
        avatarType: '专业讲师',
        totalDuration: 50,
        actCount: 5,
        duration: 10
      }
    },

    // PPT影片
    scenario5_ppt_video: {
      name: 'PPT影片 - 专用模板',
      description: '基于PPT内容生成配音脚本',
      template: `你是专业的PPT讲解编剧Tony。用户上传了PPT内容，需要你为每一页设计专业的讲解文案。

PPT内容概述：{userInput}
总页数：{slideCount}
每页讲解时长：{slidesDuration}秒

请为PPT的每一页生成讲解文案，要求：
1. 文案要与PPT内容高度匹配
2. 语言要专业但易懂
3. 每页都要有承上启下的过渡
4. 重点内容要重复强调
5. 最后要有总结回顾

返回格式：
{
  "title": "PPT演示标题",
  "presentation_type": "演示类型",
  "total_duration": {totalDuration},
  "slides": [
    {
      "slide_number": 1,
      "content": "第一页的讲解文案",
      "duration": {slidesDuration},
      "key_points": ["要点1", "要点2"],
      "transition": "到下一页的过渡语"
    }
  ]
}`,
      variables: ['userInput', 'slideCount', 'slidesDuration', 'totalDuration'],
      defaults: {
        slideCount: 5,
        slidesDuration: 20,
        totalDuration: 100
      }
    }
  },

  // ========== AI生图提示词 ==========
  [PROMPT_CATEGORIES.IMAGE_GENERATION]: {
    business_style: {
      name: '商务风格图片',
      description: '生成专业的商务场景图片',
      template: `Create a professional business image: {description}

Style requirements:
- Clean, modern business aesthetic
- Professional lighting
- High-quality, crisp details
- Corporate environment
- Color scheme: {colorScheme}
- Aspect ratio: {aspectRatio}
- Resolution: {resolution}

Additional specifications:
- No text or watermarks
- Professional composition
- Suitable for corporate presentations`,
      variables: ['description', 'colorScheme', 'aspectRatio', 'resolution'],
      defaults: {
        colorScheme: 'blue and white',
        aspectRatio: '16:9',
        resolution: '1920x1080'
      }
    },

    educational_style: {
      name: '教育风格图片',
      description: '生成适合教学的图片',
      template: `Generate an educational illustration: {description}

Requirements:
- Clear, easy to understand visuals
- Bright, engaging colors
- Simplified, clean design
- Educational context
- Style: {illustrationStyle}
- Target audience: {audience}
- Focus on: {focusElement}

Visual elements:
- No complex details that distract from learning
- Use icons and symbols where appropriate
- Maintain visual hierarchy`,
      variables: ['description', 'illustrationStyle', 'audience', 'focusElement'],
      defaults: {
        illustrationStyle: 'flat design',
        audience: 'adults',
        focusElement: 'main concept'
      }
    }
  },

  // ========== 标题生成提示词 ==========
  [PROMPT_CATEGORIES.TITLE_GENERATION]: {
    viral_titles: {
      name: '爆款标题生成',
      description: '生成吸引人的病毒式传播标题',
      template: `你是短视频标题专家，专门创作爆款标题。请为以下主题生成{titleCount}个吸引人的短视频标题：

主题：{topic}
平台：{platform}
目标观众：{audience}

标题要求：
1. 长度控制在{minLength}-{maxLength}字之间
2. 必须包含数字或时间
3. 激发好奇心和情感共鸣
4. 适合{platform}平台的用户习惯
5. 包含热门关键词：{keywords}

标题类型偏好：
- 疑问式：30%
- 数字式：40%
- 对比式：20%
- 情感式：10%

请按以下格式返回：
1. 标题一
2. 标题二
...
{titleCount}. 标题{titleCount}`,
      variables: ['topic', 'platform', 'audience', 'titleCount', 'minLength', 'maxLength', 'keywords'],
      defaults: {
        platform: '抖音',
        audience: '18-35岁年轻人',
        titleCount: 10,
        minLength: 15,
        maxLength: 25,
        keywords: '干货,技巧,方法'
      }
    }
  },

  // ========== 内容优化提示词 ==========
  [PROMPT_CATEGORIES.OPTIMIZATION]: {
    content_polish: {
      name: '内容优化润色',
      description: '优化和润色现有内容',
      template: `你是内容优化专家，请对以下内容进行专业润色：

原始内容：{originalContent}
优化目标：{goal}
目标观众：{audience}
内容类型：{contentType}

优化要求：
1. 保持原意不变
2. 提升表达的清晰度和吸引力
3. 优化语言节奏和流畅度
4. 增强情感感染力
5. 确保适合{contentType}的特点

请返回优化后的内容，并说明主要改进点。`,
      variables: ['originalContent', 'goal', 'audience', 'contentType'],
      defaults: {
        goal: '提升吸引力',
        audience: '通用观众',
        contentType: '短视频脚本'
      }
    }
  }
}

/**
 * 获取提示词模板
 * @param {string} category - 提示词类别
 * @param {string} templateName - 模板名称
 * @returns {Object|null} 提示词模板对象
 */
export function getPromptTemplate(category, templateName) {
  try {
    const categoryTemplates = PROMPT_TEMPLATES[category]
    if (!categoryTemplates) {
      console.warn(`提示词类别不存在: ${category}`)
      return null
    }

    const template = categoryTemplates[templateName]
    if (!template) {
      console.warn(`提示词模板不存在: ${category}.${templateName}`)
      return null
    }

    return { ...template }
  } catch (error) {
    console.error('获取提示词模板失败:', error)
    return null
  }
}

/**
 * 渲染提示词模板
 * @param {string} category - 提示词类别
 * @param {string} templateName - 模板名称
 * @param {Object} variables - 变量值
 * @returns {string} 渲染后的提示词
 */
export function renderPrompt(category, templateName, variables = {}) {
  console.log(`[${new Date().toLocaleTimeString()}] 🎨 开始渲染提示词模板`)
  console.log(`[${new Date().toLocaleTimeString()}] 📋 类别: ${category}, 模板: ${templateName}`)

  try {
    const template = getPromptTemplate(category, templateName)
    if (!template) {
      throw new Error(`模板不存在: ${category}.${templateName}`)
    }

    // 合并默认值和用户提供的变量
    const mergedVariables = {
      ...template.defaults,
      ...variables
    }

    console.log(`[${new Date().toLocaleTimeString()}] 📊 变量数量: ${Object.keys(mergedVariables).length}`)

    // 渲染模板
    let renderedPrompt = template.template

    // 替换所有变量
    Object.entries(mergedVariables).forEach(([key, value]) => {
      const regex = new RegExp(`\\{${key}\\}`, 'g')
      renderedPrompt = renderedPrompt.replace(regex, value)
    })

    // 检查是否还有未替换的变量
    const unreplacedVars = renderedPrompt.match(/\{[^}]+\}/g)
    if (unreplacedVars) {
      console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ 存在未替换的变量:`, unreplacedVars)
    }

    console.log(`[${new Date().toLocaleTimeString()}] ✅ 提示词渲染完成`)
    console.log(`[${new Date().toLocaleTimeString()}] 📊 渲染后长度: ${renderedPrompt.length}字符`)

    return renderedPrompt

  } catch (error) {
    console.error(`[${new Date().toLocaleTimeString()}] ❌ 提示词渲染失败:`, error)
    throw new Error(`提示词渲染失败: ${error.message}`)
  }
}

/**
 * 获取类别下的所有模板
 * @param {string} category - 提示词类别
 * @returns {Array} 模板列表
 */
export function getTemplatesByCategory(category) {
  const categoryTemplates = PROMPT_TEMPLATES[category]
  if (!categoryTemplates) {
    return []
  }

  return Object.entries(categoryTemplates).map(([key, template]) => ({
    key,
    name: template.name,
    description: template.description,
    variables: template.variables || [],
    defaults: template.defaults || {}
  }))
}

/**
 * 获取所有可用的提示词类别
 * @returns {Array} 类别列表
 */
export function getAllCategories() {
  return Object.entries(PROMPT_CATEGORIES).map(([key, value]) => ({
    key,
    value,
    name: getCategoryDisplayName(value)
  }))
}

/**
 * 获取类别的显示名称
 * @param {string} category - 类别值
 * @returns {string} 显示名称
 */
function getCategoryDisplayName(category) {
  const displayNames = {
    [PROMPT_CATEGORIES.SCRIPTWRITING]: 'AI编剧',
    [PROMPT_CATEGORIES.IMAGE_GENERATION]: 'AI生图',
    [PROMPT_CATEGORIES.VOICE_SYNTHESIS]: '语音合成',
    [PROMPT_CATEGORIES.VIDEO_EDITING]: '视频剪辑',
    [PROMPT_CATEGORIES.TITLE_GENERATION]: '标题生成',
    [PROMPT_CATEGORIES.OPTIMIZATION]: '内容优化'
  }
  
  return displayNames[category] || category
}

/**
 * 验证模板变量
 * @param {Object} template - 提示词模板
 * @param {Object} variables - 提供的变量
 * @returns {Object} 验证结果
 */
export function validateTemplateVariables(template, variables) {
  const result = {
    isValid: true,
    missingVariables: [],
    extraVariables: []
  }

  if (!template || !template.variables) {
    return result
  }

  // 检查缺少的必需变量
  const requiredVars = template.variables || []
  const providedVars = Object.keys(variables)

  result.missingVariables = requiredVars.filter(varName => 
    !providedVars.includes(varName) && 
    !(template.defaults && template.defaults[varName])
  )

  // 检查多余的变量
  result.extraVariables = providedVars.filter(varName => 
    !requiredVars.includes(varName)
  )

  result.isValid = result.missingVariables.length === 0

  return result
}

/**
 * 创建自定义提示词模板
 * @param {string} category - 类别
 * @param {string} templateName - 模板名称
 * @param {Object} templateData - 模板数据
 * @returns {boolean} 是否创建成功
 */
export function createCustomTemplate(category, templateName, templateData) {
  try {
    console.log(`[${new Date().toLocaleTimeString()}] 🆕 创建自定义模板: ${category}.${templateName}`)

    if (!PROMPT_TEMPLATES[category]) {
      PROMPT_TEMPLATES[category] = {}
    }

    PROMPT_TEMPLATES[category][templateName] = {
      name: templateData.name || templateName,
      description: templateData.description || '自定义模板',
      template: templateData.template,
      variables: templateData.variables || [],
      defaults: templateData.defaults || {},
      isCustom: true,
      createdAt: new Date().toISOString()
    }

    console.log(`[${new Date().toLocaleTimeString()}] ✅ 自定义模板创建成功`)
    return true

  } catch (error) {
    console.error(`[${new Date().toLocaleTimeString()}] ❌ 自定义模板创建失败:`, error)
    return false
  }
}

/**
 * 导出提示词管理器的主要接口
 */
export default {
  PROMPT_CATEGORIES,
  getPromptTemplate,
  renderPrompt,
  getTemplatesByCategory,
  getAllCategories,
  validateTemplateVariables,
  createCustomTemplate
}

