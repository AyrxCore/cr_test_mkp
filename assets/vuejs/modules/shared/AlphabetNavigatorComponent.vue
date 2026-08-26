<template>
  <div
    ref="letterIndexRef"
    class="letter-index"
    :class="{ 'letter-index-floating': isFloatingActive }"
  >
    <button
      v-for="letter in allLetters"
      :key="letter"
      @click="scrollToLetter(letter)"
      class="letter-button"
      :class="{
        active: activeLetter === letter,
        inactive: !isLetterAvailable(letter),
        'floating-letter-button': isFloatingActive,
      }"
      :disabled="!isLetterAvailable(letter)"
    >
      {{ letter }}
    </button>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  itemKey: {
    type: String,
    default: 'name',
  },
  activationOffset: {
    type: Number,
    default: -50,
  },
  topThreshold: {
    type: Number,
    default: 60,
  },
})

const emit = defineEmits(['update:floatingStatus'])

const activeLetter = ref<string | null>(null)
const isScrolling = ref<boolean>(false)
const isFloatingActive = ref<boolean>(false)
const letterIndexRef = ref<HTMLElement | null>(null)
const scrollTimeout = ref<number>(0)
const originalPosition = ref<number>(0)

const allLetters = computed((): string[] => {
  return 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')
})

const availableLetters = computed((): string[] => {
  const letters = new Set<string>()
  props.items?.forEach((item) => {
    const firstLetter = item[props.itemKey].charAt(0).toUpperCase()
    if (/[A-Z]/.test(firstLetter)) {
      letters.add(firstLetter)
    }
  })
  return Array.from(letters).sort()
})

const isLetterAvailable = (letter: string): boolean => {
  return availableLetters.value.includes(letter)
}

const detectSectionInView = () => {
  const sections = document.querySelectorAll('[data-letter-section]')
  const middlePosition = window.innerHeight / 2

  const scrollY = window.scrollY

  isFloatingActive.value =
    letterIndexRef.value &&
    scrollY > originalPosition.value + props.activationOffset &&
    scrollY > props.topThreshold

  emit('update:floatingStatus', isFloatingActive.value)

  let closestSection = null
  let closestDistance = Infinity

  sections.forEach((section) => {
    const rect = section.getBoundingClientRect()
    const distance = Math.abs(rect.top + rect.height / 2 - middlePosition)

    if (distance < closestDistance) {
      closestDistance = distance
      closestSection = section
    }
  })

  if (closestSection) {
    const id = closestSection.id
    const letter = id.replace('letter-', '')

    if (letter && letter !== activeLetter.value) {
      activeLetter.value = letter
    }
  }
}

const scrollToLetter = (letter: string) => {
  if (!isLetterAvailable(letter)) return

  activeLetter.value = letter
  isScrolling.value = true

  const target = document.getElementById(`letter-${letter}`)
  if (target) {
    const nav = document.querySelector('nav')
    const navHeight = nav ? nav.offsetHeight : 0
    const targetPosition = target.getBoundingClientRect().top + window.scrollY
    const scrollPosition = targetPosition - navHeight - 20

    window.scrollTo({
      top: scrollPosition,
      behavior: 'smooth',
    })
  }

  clearTimeout(scrollTimeout.value)
  scrollTimeout.value = setTimeout(() => {
    isScrolling.value = false
  }, 1000)
}

const adjustLetterIndexPosition = () => {
  if (isFloatingActive.value && letterIndexRef.value) {
    const containerHeight = window.innerHeight
    const letterIndexHeight = letterIndexRef.value.offsetHeight

    if (letterIndexHeight > containerHeight - 100) {
      letterIndexRef.value.style.maxHeight = `${containerHeight - 100}px`
      letterIndexRef.value.style.overflowY = 'auto'
    }
  }
}

const handleScroll = () => {
  detectSectionInView()
  adjustLetterIndexPosition()
}

const handleResize = () => {
  adjustLetterIndexPosition()
}

onMounted(() => {
  if (letterIndexRef.value) {
    originalPosition.value =
      letterIndexRef.value.getBoundingClientRect().top + window.scrollY
  }

  window.addEventListener('scroll', handleScroll)
  window.addEventListener('resize', handleResize)

  detectSectionInView()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('resize', handleResize)
  clearTimeout(scrollTimeout.value)
})
</script>

<style scoped>
@reference '@/style/main.css';

.letter-index {
  @apply hidden gap-[16px] rounded-md bg-white p-2 py-4 transition-all duration-[0.3s] ease-[ease] md:mb-4 md:flex md:flex-wrap md:gap-[5px];
}

.letter-index-floating {
  @apply fixed z-[29] overflow-y-auto rounded-lg border border-solid border-secondary bg-white shadow-[0_0_20px_rgba(0,0,0,0.1)];
  @apply right-2.5 top-[61%] flex max-h-[55vh] -translate-y-2/4 flex-col gap-1 p-1.5;
  @apply md:sticky md:left-0 md:right-auto md:top-[170px] md:max-h-none  md:w-full md:translate-y-0 md:flex-row md:gap-[5px] md:p-2 md:shadow-md;
}

.letter-button {
  @apply flex-[0.5] cursor-pointer rounded text-center transition-all duration-[0.3s] ease-[ease];
}

.floating-letter-button {
  @apply flex h-[30px] w-[30px] min-w-[30px] items-center justify-center p-0 text-[0.85rem] md:h-auto md:w-auto md:min-w-0 md:p-0 md:text-lg;
}

.letter-button:hover {
  @apply bg-[#e0e0e0];
}

.letter-button.active {
  @apply bg-secondary px-1.5 py-1.5 text-white md:px-0;
}

.letter-button.inactive {
  @apply pointer-events-none cursor-not-allowed text-gray-300;
}

.letter-index-floating::-webkit-scrollbar {
  @apply w-1;
}

.letter-index-floating::-webkit-scrollbar-track {
  @apply rounded bg-[#f0f0f07f];
}

.letter-index-floating::-webkit-scrollbar-thumb {
  @apply rounded bg-[#b4b4b4cc];
}
</style>
