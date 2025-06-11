<template>
  <a-row justify="center" align="center" style="padding: 0 5%">
    <a-form :model="form" auto-label-width class="w-full">
      <a-form-item label="文件名称" field="name" :rules="[{ required: true, message: '请输入文件名称' }]" style="margin-bottom: 0">
        <a-input v-model="form.name" placeholder="请输入文件名称" allow-clear />
      </a-form-item>
    </a-form>
  </a-row>
</template>

<script lang="ts" setup>
const form = ref({
  id: '',
  name: ''
})

const setForm = (obj: { id: string, name: string }) => {
  Object.assign(form.value, obj)
}

defineExpose({ setForm, getFormData: () => ({ ...form.value }) })
</script>

<style lang="scss" scoped></style>
