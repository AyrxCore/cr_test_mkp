<template>
  <Transition name="fade">
    <div v-if="focused || password !== ''" class="mt-2 rounded-md border border-gray-200 bg-gray-50 p-3 text-sm">
      <p class="mb-2 font-medium text-gray-700">Consignes de sécurité :</p>
      <ul class="space-y-1.5">
        <li
          v-for="rule in rules"
          :key="rule.label"
          :class="rule.valid ? 'text-green-600' : 'text-red-500'"
          class="flex items-center gap-2 transition-colors duration-200"
        >
          <CheckCircleIcon v-if="rule.valid" class="shrink-0" />
          <XCircleIcon v-else class="shrink-0" />
          {{ rule.label }}
        </li>
      </ul>
    </div>
  </Transition>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { PASSWORD_RULES } from '@/vuejs/composables/usePasswordStrength'
import CheckCircleIcon from '@/vuejs/modules/shared/icon/PasswordCheckIconComponent.vue'
import XCircleIcon from '@/vuejs/modules/shared/icon/PasswordXIconComponent.vue'

const props = defineProps({
  password: {
    type: String,
    required: true,
  },
  focused: {
    type: Boolean,
    default: false,
  },
})

const rules = computed(() =>
  PASSWORD_RULES.map((rule) => ({
    label: rule.label,
    valid: rule.test(props.password),
  }))
)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
