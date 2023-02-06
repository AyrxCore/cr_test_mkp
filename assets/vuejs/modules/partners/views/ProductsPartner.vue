<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div
      v-if="accord"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8"
    >

      <HeaderPartnerComponent
        :name="accord.name"
        :note="accord.properties.note_rse"
        :logo="accord.properties.logo_partenaire"
        :barner="accord.properties.banniere_partenaire"
        :categories="accord.categories"
      />

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
        <div class="mr-2">{{ productsSimilaire.length + 1 }} produits</div>
        <div class="h-[28px] rounded-md border bg-white">
          <select class="h-[28px] rounded-md py-0 text-[14px]">
            <option>Trier par produit</option>
          </select>
        </div>
      </div>
      <div
        class="mt-10 mt-5 flex flex-col gap-4 text-gray-600 xl:grid xl:grid-cols-5"
      >
        <DropdownListComponent>
          <template #button-label> Filtres </template>
          <template #content>
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
          </template>
        </DropdownListComponent>

        <div class="col-span-4 flex flex-col rounded-lg pb-4 text-gray-500">
          <div
            class="flex flex-col text-gray-600 md:grid md:grid-cols-2 md:gap-8 lg:grid-cols-3"
          >
            <div
              class="flex h-[516px] w-auto flex-col rounded-md bg-primary px-8 pt-8"
            >
              <div
                class="mx-auto flex h-[273px] items-center justify-center bg-white"
              >
                <img
                  :src="getImage(alda)"
                  alt="Image produit"
                  class="flex h-[auto!important]"
                />
              </div>
              <p class="my-7 flex text-lg font-normal text-white">
                Découvrez ou téléchargez les conditions négociées de ce
                partenaire
              </p>
              <RouterLink
                :to="{ path: '/app/partner' }"
                class="button button-white button-white-primary flex"
              >
                <ArrowRightIconComponent />Découvrir l'accord cadre
              </RouterLink>
            </div>
            <div>
              <ProductComponent
                :product="productsTopVenteHomepage[0]"
                class="mt-5 h-[516px] !w-auto md:mt-0 md:w-[392px]"
              />
            </div>
            <div v-for="(product, key) in productsSimilaire" :key="key">
              <ProductComponent
                :product="product"
                class="mt-5 h-[516px] !w-auto md:mt-0 md:w-[392px]"
              />
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
import CheckboxComponent from '@/vuejs/modules/shared/CheckboxComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'
import {
  productsSimilaire,
  productsTopVenteHomepage,
} from '@/vuejs/modules/products'
import alda from '@/vuejs/assets/img/demo/alda-partner.png'
import { getImage } from '@/vuejs/services/utils'
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import { AccordCadre } from '@/vuejs/types/AccordCadre'
import { useRoute } from 'vue-router'
import { useAccordCadreStore } from '@/vuejs/stores/accord_cadre'
import { computed, ref, watch } from 'vue'

const route = useRoute()
const accordStore = useAccordCadreStore()

const accord = ref<AccordCadre>()

const breadcrumbUrl = computed(() => {
  return []
})

watch(
  () => route.params.id as string,
  async (id: string) => {
    if (id) accord.value = await accordStore.findAccordCadreById(id)
  },
  { immediate: true },
)
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white px-7.5 py-7.5 text-gray-500;
}
</style>
