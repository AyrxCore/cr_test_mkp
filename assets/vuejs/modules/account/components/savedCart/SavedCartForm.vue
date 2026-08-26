<template>
  <div>
    <div class="flex flex-col justify-start">
      <div
        class="mb-6 flex flex-col md:block"
        :class="{
          'flex items-center': !formCol,
        }"
      >
        <input
          v-model="savedCartName"
          type="text"
          placeholder="Nom de votre panier sauvegardé *"
          class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 text-gray-600 text-primary placeholder-gray-400"
          required
          @input="emit('updateSavedCartName', savedCartName)"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, PropType, ref } from 'vue'
import { SavedCart } from '@/vuejs/types/SavedCart'

const props = defineProps({
  savedCart: {
    type: Object as PropType<SavedCart>,
    required: true,
    default: null,
  },
  formCol: {
    type: Boolean,
    required: false,
    default: false,
  },
})

const emit = defineEmits(['updateSavedCartName'])
const savedCartName = ref<string | null>(null)

onMounted(() => {
  savedCartName.value = props.savedCart.name
})
</script>

<style scoped></style>
