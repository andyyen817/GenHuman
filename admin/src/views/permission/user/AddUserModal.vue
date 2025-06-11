<template>
  <a-modal v-model:visible="visible" :title="title" width="90%" :mask-closable="false"
    :modal-style="{ maxWidth: '600px' }" :body-style="{ maxHeight: '70vh' }" @before-ok="save" @close="close">
    <a-form ref="formRef" :model="form" :rules="rules" size="medium" auto-label-width>
      <a-row>
        <a-col v-bind="{ xs: 24, sm: 24, md: 24, lg: 24, xl: 24, xxl: 24 }">
          <a-form-item label="头像" field="avatar" v-if="visible">
            <YUpload v-model="form.avatar"></YUpload>
          </a-form-item>
        </a-col>
        <a-col v-bind="col2Props">
          <a-form-item label="用户名" field="username">
            <a-input v-model.trim="form.username" placeholder="请输入用户名" allow-clear ></a-input>
          </a-form-item>
        </a-col>
        <a-col v-bind="col2Props">
          <a-form-item label="昵称" field="nickname">
            <a-input v-model.trim="form.nickname" placeholder="请输入昵称" allow-clear ></a-input>
          </a-form-item>
        </a-col>
        <a-col v-bind="col2Props">
          <a-form-item label="密码" field="password">
            <a-input-password v-model.trim="form.password" placeholder="请输入密码,为空不修改" allow-clear></a-input-password>
          </a-form-item>
        </a-col>

        <a-col v-bind="col2Props">
          <a-form-item label="角色" field="role_id">
            <a-select v-model="form.role_id" :options="roleOptions" value-key="id" placeholder="请选择所属角色" allow-clear
              :allow-search="{ retainInputValue: true }" />
          </a-form-item>
        </a-col>

      </a-row>

      <a-form-item label="描述" field="description">
        <a-textarea v-model.trim="form.description" :max-length="200" placeholder="请填写描述" :auto-size="{ minRows: 3 }"
          show-word-limit />
      </a-form-item>

      <a-form-item label="状态" field="status">
        <a-switch v-model="form.status" type="round" :checked-value="1" :unchecked-value="0" checked-text="正常"
          unchecked-text="禁用" />
      </a-form-item>
    </a-form>
  </a-modal>
</template>

<script setup lang="ts">
import { type ColProps, type FormInstance, Message } from '@arco-design/web-vue'
import * as Regexp from '@/utils/regexp'
import { getUserDetail, saveUser } from '@/apis/permission'
import { useRole } from '@/hooks/app'
import { useResetReactive } from '@/hooks'

const emit = defineEmits<{
  (e: 'save-success'): void
}>()

const { roleList, getRoleList } = useRole()
getRoleList()
const roleOptions = computed(() => roleList.value.map((i) => ({ label: i.title, value: i.id })))


const col2Props: ColProps = { xs: 24, sm: 24, md: 12, lg: 12, xl: 12, xxl: 12 }
const formRef = useTemplateRef('formRef')
const userId = ref('')
const isEdit = computed(() => !!userId.value)
const title = computed(() => (isEdit.value ? '编辑用户' : '新增用户'))
const visible = ref(false)
const [form, resetForm] = useResetReactive({
  id: '',
  avatar: '',
  password: '',
  username: '', // 用户名
  nickname: '', // 昵称
  role_id: '', // 角色(可能多个)
  description: '', // 描述
  status: 1 as Status, // 状态 0禁用 1启用(正常)
  type: 2 as 1 | 2, // 类型 1系统内置(admin是系统内置) 2自定义
})

const rules: FormInstance['rules'] = {
  username: [
    { required: true, message: '请输入用户名' },
    { min: 2, max: 4, message: '长度在 2 - 4个字符' }
  ],
  nickname: [
    { required: true, message: '请输入昵称' },
    { min: 2, max: 4, message: '长度在 2 - 4个字符' }
  ],
  email: [{ match: Regexp.Email, message: '邮箱格式不正确' }],
  phone: [{ match: Regexp.Phone, message: '手机号格式不正确' }],
  deptId: [{ required: true, message: '请选择所属部门' }],
  role_id: [{ required: true, message: '请选择角色' }],
  status: [{ required: true, message: '请选择状态' }]
}

const add = () => {
  userId.value = ''
  visible.value = true
}

const edit = async (id: string) => {
  visible.value = true
  userId.value = id
  const res = await getUserDetail({ id })
  Object.assign(form, res.data)
}

const close = () => {
  formRef.value?.resetFields()
  resetForm()
}

const save = async () => {
  try {
    const valid = await formRef.value?.validate()
    if (valid) return false
    const res = await await saveUser(form)
    if (res) {
      Message.success('保存成功')
      emit('save-success')
      return true
    } else {
      return false
    }
  } catch (error) {
    return false
  }
}

defineExpose({ add, edit })
</script>
