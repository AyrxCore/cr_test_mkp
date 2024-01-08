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
import { RouteRecordName, useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  routeName: {
    required: true,
    type: String,
  },
})

const classes = computed((): string[] => {
  if (props.routeName === currentRouteName.value) {
    return ['border-secondary', 'text-secondary']
  }
  return ['border-black', 'text-black']
})

const currentRouteName = computed(
  (): RouteRecordName => router.currentRoute.value.name,
)
</script>

<style scoped></style>
