<template>
  <div
    class="mt-2 border-b-2 px-6 text-sm sm:mt-0 md:text-base lg:text-lg"
    :class="classes"
  >
    <slot />
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  routeName: {
    required: true,
    type: [String, Array<string>],
  },
})

const classes = computed((): string[] => {
  if (props.routeName.includes(currentRouteName.value)) {
    return ['border-secondary', 'text-secondary']
  }
  return ['border-black', 'text-black']
})

const currentRouteName = computed(
  (): string => router.currentRoute.value.name as string,
)
</script>

<style scoped></style>
