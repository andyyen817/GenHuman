import{d as I,h as d,B as T,j as A,i as x,c as n,q as V,k as t,l as f,t as r,w as P,m as S,n as B,F as q,y as D,u as M,o}from"./vendor-PMV8qmst.js";import{_ as N}from"./index-D05N9H6s.js";const $={class:"scriptwriter-container"},F={class:"scriptwriter-simple"},G={class:"tony-greeting"},R={class:"greeting-bubble"},W={class:"writing-area"},j={class:"writing-tools"},E=["disabled"],H={key:0},L={key:1},U={key:0,class:"ai-generating"},z={class:"step-footer"},J=["disabled"],K={class:"modal-content"},O={class:"template-grid"},Q=["onClick"],X={class:"template-icon"},Y=I({__name:"ScriptwriterView",setup(Z){const v=M(),g=T(),_=d(g.query.scenario||"fromScratch"),i=d(""),l=d(!1),c=d(!1),p={digitalHuman:{title:"你好，你選擇了快速製作數字人",content:"我是你的Vidspark AI專屬編劇 Tony，請你上傳你現有的文案，我來幫你把文案分鏡到數字人影片"},pptVideo:{title:"你好，你選擇了製作PPT影片",content:"我是你的Vidspark AI專屬編劇 Tony，請你上傳你現有的PPT，我來根據PPT，幫你設計PPT每一頁文案"},videoClip:{title:"你好，你選擇了快速切片",content:"我是你的Vidspark AI專屬編劇 Tony，請你上傳你想切片的影片，影片需要有音頻，我來幫你切出精采片段"},default:{title:"📝 第一步：写剧本",content:"告诉AI你想讲什么故事，或者让AI帮你写"}},u=A(()=>p[_.value]||p.default),m=x([{id:1,icon:"📚",title:"教学介绍",description:"适合介绍知识、技能、产品的教学视频",content:`第一幕：我是[你的名字]，今天要和大家分享[主题]

第二幕：首先我们来了解一下[核心概念]，这个很重要因为[原因]

第三幕：接下来我来演示[具体步骤]，大家跟着我一起做

第四幕：总结一下，我们学会了[要点总结]，希望对大家有帮助`},{id:2,icon:"💡",title:"故事叙述",description:"用故事的方式传达道理或经验",content:`第一幕：从前有一个[人物]，他遇到了[问题]

第二幕：为了解决这个问题，他尝试了[方法]，但是[遇到困难]

第三幕：后来他发现了[关键方法]，通过[具体行动]解决了问题

第四幕：这个故事告诉我们[道理]，在生活中我们也可以[应用]`},{id:3,icon:"🎯",title:"产品介绍",description:"专业介绍产品特点和优势",content:`第一幕：大家好，今天给大家介绍[产品名称]

第二幕：这个产品最大的特点是[核心特色]，可以帮你[解决问题]

第三幕：具体的使用方法很简单，只需要[操作步骤]

第四幕：相比其他产品，我们的优势是[优势对比]，欢迎大家试用`}]),b=async()=>{if(!l.value){l.value=!0,console.log("🤖 [AI写作] 开始生成剧本");try{await new Promise(e=>setTimeout(e,3e3)),i.value=`第一幕：大家好，我是时间管理小助手，今天要和大家分享几个超实用的时间管理技巧

第二幕：首先是番茄工作法，把工作分成25分钟的小块，这样能保持专注力

第三幕：接下来是优先级排序，把重要紧急的事情放在第一位，避免被琐事干扰

第四幕：最后要记住，时间管理不是为了忙碌，而是为了有时间做真正重要的事情

第五幕：希望这些方法对大家有帮助，记得点赞关注哦！`,console.log("✅ [AI写作] 剧本生成完成")}catch(e){console.error("❌ [AI写作] 生成失败:",e),alert("AI生成失败，请重试")}finally{l.value=!1}}},k=()=>{console.log("✏️ [自己写作] 用户选择自己写");const e=document.querySelector(".script-textarea");e&&e.focus()},h=()=>{console.log("📋 [模板选择] 显示模板弹窗"),c.value=!0},y=e=>{console.log("📋 [模板选择] 选择模板:",e.title),i.value=e.content,c.value=!1},w=()=>{console.log("← [返回] 回到首页"),v.push("/")},C=()=>{if(!i.value.trim()){alert("请先写好剧本再进入下一步！");return}console.log("→ [下一步] 进入角色设计"),v.push("/director")};return(e,s)=>(o(),n("div",$,[s[6]||(s[6]=V('<div class="simple-progress" data-v-a22825f7><div class="progress-steps" data-v-a22825f7><div class="step active" data-v-a22825f7><span class="step-icon" data-v-a22825f7>📝</span><span class="step-text" data-v-a22825f7>第1步：写剧本</span></div><div class="step-arrow" data-v-a22825f7>→</div><div class="step" data-v-a22825f7><span class="step-icon" data-v-a22825f7>👤</span><span class="step-text" data-v-a22825f7>第2步：选人物</span></div><div class="step-arrow" data-v-a22825f7>→</div><div class="step" data-v-a22825f7><span class="step-icon" data-v-a22825f7>🎤</span><span class="step-text" data-v-a22825f7>第3步：配声音</span></div></div><div class="current-indicator" data-v-a22825f7>当前：📝 写剧本</div></div>',1)),t("main",F,[t("div",G,[s[3]||(s[3]=t("div",{class:"tony-avatar"},[t("div",{class:"tony-face"},"👨‍💻")],-1)),t("div",R,[t("h2",null,r(u.value.title),1),t("p",null,r(u.value.content),1)])]),t("div",W,[P(t("textarea",{"onUpdate:modelValue":s[0]||(s[0]=a=>i.value=a),class:"script-textarea",placeholder:`在这里写你的剧本...

例如：
• 第一幕：小明走进教室，发现桌上有一本神奇的书
• 第二幕：书本开始发光，带小明进入了奇幻世界  
• 第三幕：小明学会了时间管理，回到现实世界

你也可以说：'帮我写一个关于时间管理的故事'`,rows:"12"},null,512),[[S,i.value]]),t("div",j,[t("button",{class:"big-btn",onClick:b,disabled:l.value},[l.value?(o(),n("span",H,"🤖 AI正在思考...")):(o(),n("span",L,"🤖 让AI帮我写"))],8,E),t("button",{class:"big-btn",onClick:k}," ✏️ 我自己写 "),t("button",{class:"big-btn",onClick:h}," 📋 使用模板 ")]),l.value?(o(),n("div",U,[...s[4]||(s[4]=[t("div",{class:"generating-animation"},[t("div",{class:"dot"}),t("div",{class:"dot"}),t("div",{class:"dot"})],-1),t("p",null,"AI正在为你创作剧本，请稍等...",-1)])])):f("",!0)]),t("div",z,[t("button",{class:"back-btn",onClick:w}," ← 返回首页 "),t("button",{class:"next-btn",onClick:C,disabled:!i.value.trim()}," 下一步：选择人物 → ",8,J)])]),c.value?(o(),n("div",{key:0,class:"template-modal",onClick:s[2]||(s[2]=B(a=>c.value=!1,["self"]))},[t("div",K,[s[5]||(s[5]=t("h3",null,"📋 选择剧本模板",-1)),t("div",O,[(o(!0),n(q,null,D(m,a=>(o(),n("div",{key:a.id,class:"template-card",onClick:tt=>y(a)},[t("div",X,r(a.icon),1),t("h4",null,r(a.title),1),t("p",null,r(a.description),1)],8,Q))),128))]),t("button",{class:"close-btn",onClick:s[1]||(s[1]=a=>c.value=!1)},"关闭")])])):f("",!0)]))}}),at=N(Y,[["__scopeId","data-v-a22825f7"]]);export{at as default};
