<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl">
      <HeaderPartnerComponent />

      <div
        class="mt-10 mb-7.5 flex flex-row items-center justify-end text-[14px] text-gray-500"
      >
        <div
          class="mr-2 flex h-[28px] w-[84px] flex-row items-center justify-between rounded-md border bg-white"
        >
          <button class="flex">
            <ChevronLeftIconComponent
              class="ml-1 h-4"
              :stroke-color="'#A4A4A4'"
            />
          </button>
          <span>1</span>
          <button class="flex">
            <ChevronRightIconComponent
              class="ml-1 h-4"
              :stroke-color="'#A4A4A4'"
            />
          </button>
        </div>
        <div class="mr-2">{{ listProducts.length }} produits</div>
        <div class="h-[28px] rounded-md border bg-white">
          <select class="h-[28px] rounded-md py-0 text-[14px]">
            <option>Trier par produit</option>
          </select>
        </div>
      </div>
      <div class="mt-10 mt-5 flex grid grid-cols-5 gap-4 text-gray-600">
        <div class="h-max rounded-lg bg-white px-7.5 pt-7.5 pb-4 text-lg">
          <h3 class="text-[25px] text-primary">Catégorie</h3>

          <div v-for="i in 3" :key="i" class="mt-5 mb-6">
            <h3 class="mb-5 text-[25px] text-primary">Filtre n°{{ i }}</h3>
            <CheckboxComponent
              v-for="j in 4"
              :key="j"
              :position-after="true"
              class="flex flex-row items-center"
            >
              <template #label-after>
                <span class="text-lg text-gray-500">Filtre 0{{ j }}</span>
              </template>
            </CheckboxComponent>
          </div>
        </div>
        <div class="col-span-4 flex flex-col rounded-lg pb-4 text-gray-500">
          <div class="flex grid grid-cols-3 gap-8 text-gray-600">
            <div
              class="flex h-[516px] flex-col rounded-md bg-primary px-8 pt-8"
            >
              <div
                class="mx-auto flex h-[273px] items-center justify-center bg-white"
              >
                <img
                  :src="defaultImageFile"
                  alt="Image produit"
                  class="flex h-[auto!important]"
                />
              </div>
              <p class="my-7 flex text-lg font-normal text-white">
                Découvrez ou téléchargez les conditions négociées de ce
                partenaire
              </p>
              <a
                href="/app/partner"
                class="button button-white button-white-primary flex"
              >
                <ArrowRightIconComponent />Découvrir l'accord cadre
              </a>
            </div>
            <div v-for="(product, key) in listProducts" :key="key">
              <ProductComponent :product="product" class="h-[516px]" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/partners/components/HeaderPartnerComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { computed } from 'vue'
import CheckboxComponent from '@/vuejs/modules/shared/CheckboxComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'

const defaultImageFile = getImage(defaultImage)

const listProducts = computed(() => {
  const products = []
  for (let i = 0; i < 8; i++) {
    products.push({
      imgFrn: defaultImageFile,
      imgProduct: defaultImageFile,
    })
  }

  return products
})
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white px-7.5 py-7.5 text-gray-500;
}
</style>
