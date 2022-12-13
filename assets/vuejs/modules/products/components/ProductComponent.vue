<template>
  <div
    class="mx-auto grid h-[516px] w-[392px] items-center justify-start rounded-md bg-white px-6 py-2"
  >
    <!-- Bloc header -->
    <div class="flex w-full items-center justify-between">
      <div class="flex h-[37px] w-[78px] items-center justify-start bg-white">
        <img
          :src="props.product.fournisseur_image"
          alt="Image produit"
          class="flex h-[100%!important]"
        />
      </div>
      <div class="flex items-center justify-end">
        <button class="flex text-gray-500">
          <HeartIconComponent class="... stroke-gray-500" />
        </button>
      </div>
    </div>
    <!-- Fin bloc header -->

    <!-- Bloc image -->
    <div
      class="mx-auto flex h-[191px!important] w-full items-center justify-center rounded-lg"
    >
      <img
        :src="props.product.image"
        alt="Image produit"
        class="flex h-[191px!important]"
      />
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full flex-col justify-start">
      <h3 class="text-left text-lg font-bold text-gray-600">
        <RouterLink :to="{ path: '/app/product' }">{{
          props.product.name
        }}</RouterLink>
      </h3>
      <p class="mt-1 w-full justify-start text-left text-lg text-gray-400">
        {{ props.product.description }}
      </p>
    </div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div class="mt-2 flex w-full items-center justify-start">
      <span class="mr-2 text-lg font-bold text-primary"
        >{{ props.product.price }}€</span
      >
      <span
        v-if="props.product.price_line_through"
        class="text-lg text-gray-400 line-through"
        >{{ props.product.price_line_through }}€ HT</span
      >
    </div>
    <!-- Fin bloc prix -->

    <!-- Bloc quantité -->
    <div class="mx-auto mt-5 flex w-full justify-between">
      <div class="justify-end">
        <span class="text-sm text-gray-500">Qté: </span>
        <select class="rounded-md border border-gray-300">
          <option v-for="i in 5" :key="i" value="{{i}}">
            {{ i }}
          </option>
        </select>
      </div>
      <ButtonComponent class="button-gradient" :is-loading="isLoading">
        <ShoppingCartIconComponent class="w-4" /> Ajouter
      </ButtonComponent>
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})

const isLoading = ref<boolean>(false)
</script>

<style scoped></style>
