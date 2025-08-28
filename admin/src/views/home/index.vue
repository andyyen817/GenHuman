<template>
  <div id="home" class="gi_page home">
    <!-- 臨時語言切換器測試 -->
    <div style="position: fixed; top: 10px; right: 10px; z-index: 1000; background: white; padding: 8px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <LanguageSwitcher />
    </div>
    
    <WorkCard></WorkCard>

    <!-- <a-row class="home__content">
      <a-col :xs="24" :sm="24" :md="24" :lg="14" :xl="14" :xxl="16">
        <div class="home__item">
          <ProjectCard></ProjectCard>
        </div>
        <div class="home__item">
          <MessageCard></MessageCard>
        </div>
      </a-col>
      <a-col :xs="24" :sm="24" :md="24" :lg="10" :xl="10" :xxl="8">
        <div class="home__item">
          <FastCard></FastCard>
        </div>
        <div class="home__item">
          <AdvCard></AdvCard>
        </div>
        <div class="home__item">
          <ImageCard></ImageCard>
        </div>
      </a-col>
    </a-row> -->

    <a-back-top :visible-height="100" target-container="#home">
      <GiSvgIcon name="backtop" :size="50" class="backtop-icon"></GiSvgIcon>
    </a-back-top>
  </div>
</template>

<script setup lang="ts">
import WorkCard from './components/WorkCard.vue'
import ProjectCard from './components/ProjectCard.vue'
import MessageCard from './components/MessageCard.vue'
import FastCard from './components/FastCard.vue'
import ImageCard from './components/ImageCard.vue'
import AdvCard from './components/AdvCard.vue'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

defineOptions({ name: 'Home' })
</script>

<style lang="scss" scoped>
.home {
  padding: 0;

  &__content {
    padding: 6px;
  }

  &__item {
    padding: 6px;
    box-sizing: border-box;
  }

  .backtop-icon {
    cursor: pointer;
  }
}
</style>
