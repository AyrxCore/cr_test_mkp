<template>
  <div>
    <div class="flex flex-col justify-start">
      <div
        class="mb-6 flex flex-col md:block"
        :class="{
          'flex items-center': !formCol,
        }"
      >
        <label
          class="flex w-full justify-start text-white md:block md:w-2/5"
          :class="{
            '!w-full text-primary': formCol,
          }"
        >
          Nom de la liste <span class="text-red-600">*</span>
        </label>
        <input
          v-model="props.savedCart.name"
          type="text"
          placeholder="Nom de votre panier sauvegardé *"
          class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 text-gray-600 text-primary placeholder-gray-400"
          required
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
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
</script>

<style scoped></style>
