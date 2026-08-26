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
            'w-full! text-primary': formCol,
          }"
        >
          Nom de la liste <span class="text-red-600">*</span>
        </label>
        <input
          v-model="selectedFavoriteName"
          type="text"
          placeholder="Le libellé de votre liste de favori *"
          class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 text-gray-600 text-primary placeholder-gray-400"
          required
          @input="emit('changeValue', selectedFavoriteName)"
        />
        <div
          v-if="errorSubmit"
          class="rounded-2xl bg-red-600 py-1 px-4 text-white"
        >
          {{ errorSubmit }}
        </div>
      </div>
      <div class="flex items-center justify-start">
        <label class="flex items-center text-white">
          <span
            class="mr-1"
            :class="{
              'w-full text-primary': formCol,
            }"
            >Visible par tous les membres:</span
          >
          <input
            v-model="selectedFavoritePublic"
            type="checkbox"
            class="mr-2 mt-1 cursor-pointer border border-white lg:mt-0"
            :class="{
              'border-primary': formCol,
            }"
            @change="emit('updateFavoritePublic', selectedFavoritePublic)"
          />
        </label>
      </div>
      <div class="mb-2">
        <small class="italic text-gray-300"
          >En cochant la case ci-dessus, cette liste sera visible par tous les
          membres de votre organisation</small
        >
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, PropType, ref } from 'vue'
import { Favorite } from '@/vuejs/types/Favorite'

const props = defineProps({
  favorite: {
    type: Object as PropType<Favorite>,
    required: true,
    default: null,
  },
  formCol: {
    type: Boolean,
    required: false,
    default: false,
  },
  errorSubmit: {
    type: String,
    required: false,
    default: null,
  },
})

const emit = defineEmits(['changeValue', 'updateFavoritePublic'])
const selectedFavoriteName = ref<string>(null)
const selectedFavoritePublic = ref<boolean>(false)

onMounted(() => {
  selectedFavoriteName.value = props.favorite.name
  selectedFavoritePublic.value = props.favorite.public
})
</script>
