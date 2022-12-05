<template>
  <RouterView v-if="props.component === ''" />
  <div v-else-if="props.component === 'login'">
    <LoginForm />
  </div>
</template>

<script lang="ts" setup>
import { onMounted } from 'vue'
import LoginForm from './modules/login/views/ExternalLoginForm.vue'

const props = defineProps({
  component: {
    required: false,
    type: String,
    default: '',
  },
})

onMounted(() => {})
</script>

<style lang="postcss">
body {
  font-family: 'CoText';
  background: #f2f0f6;
}
</style>
