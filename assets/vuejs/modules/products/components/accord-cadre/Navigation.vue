<template>
  <div
    class="navigation-wrapper f sticky top-42 z-10 rounded-full bg-gray-100 font-cotext"
  >
    <nav class="flex justify-start pl-8">
      <a
        v-for="tab in allTabs"
        :key="tab.anchor"
        :class="[
          activeTab === tab.anchor
            ? 'border-primary text-primary'
            : 'border-transparent text-gray-700',
        ]"
        class="ml-8 whitespace-nowrap border-b-4 py-4 text-lg font-bold transition-colors hover:text-primary"
        href="#"
        @click.prevent="scrollToSection(tab)"
      >
        {{ tab.name }}
      </a>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import { computed, inject, onMounted, onUnmounted, ref, Ref, watch } from 'vue'
import { storeToRefs } from 'pinia'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'
import { useScrollToElement } from '@/vuejs/services/utils.ts'
import { Tab } from '@/vuejs/types/Navigation.ts'

const { listBlocks, showStepsBlock } = storeToRefs(useAccordCadreStore())

const showMapBlock = inject<Ref<boolean | null>>('showMapBlock')
const activeTab = ref<string>(null)

const displayableBlocks = computed(() => {
  const blocks = ['negociatedTermsBlock']
  if (showStepsBlock.value) {
    blocks.push('stepsBlock')
  }
  return blocks
})

const allTabs = computed<Array<Tab>>(() => {
  const tabs = displayableBlocks.value
    .filter(
      (blockKey) =>
        listBlocks.value?.[blockKey] && listBlocks.value[blockKey]?.title,
    )
    .map((blockKey) => ({
      name: listBlocks.value[blockKey].title,
      anchor: blockKey,
    }))

  if (showMapBlock?.value === true) {
    tabs.push({ name: 'Où trouver le partenaire', anchor: 'mapBlock' })
  }

  return tabs
})

watch(
  allTabs,
  (tabs) => {
    if (tabs.length > 0 && !activeTab.value) {
      activeTab.value = tabs[0].anchor
    }
  },
  { immediate: true },
)

const NAV_OFFSET = 200
let isNavigating = false
let navigationTimer: ReturnType<typeof setTimeout> | null = null

const updateActiveTab = () => {
  if (isNavigating) return
  const tabs = allTabs.value
  let current = tabs[0]?.anchor
  for (const tab of tabs) {
    const el = document.getElementById(tab.anchor)
    if (el && el.getBoundingClientRect().top <= NAV_OFFSET) {
      current = tab.anchor
    }
  }
  if (current) activeTab.value = current
}

const scrollToSection = (tab: Tab) => {
  activeTab.value = tab.anchor
  isNavigating = true
  if (navigationTimer) clearTimeout(navigationTimer)
  navigationTimer = setTimeout(() => { isNavigating = false }, 800)
  useScrollToElement(tab.anchor)
}

onMounted(() => {
  window.addEventListener('scroll', updateActiveTab, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', updateActiveTab)
  if (navigationTimer) clearTimeout(navigationTimer)
})
</script>

<style scoped>
.navigation-wrapper::before {
  content: '';
  position: absolute;
  top: -20px;
  left: 0;
  right: 0;
  height: 20px;
  background: white;
  z-index: 1;
}

.navigation-wrapper {
  box-shadow: 0 -20px 0 0 white;
  background-clip: content-box;
}
</style>
