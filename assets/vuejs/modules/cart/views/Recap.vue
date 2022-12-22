<template>
  <CartPage>
    <template #left-side>
      <div class="">
        <h3 class="primary mb-2 text-[35px]">
          Panier <span class="uppercase">{{ detailsCart.company_name }}</span>
        </h3>
      </div>
      <div
        v-for="(partner, key) in detailsCart.partners"
        :key="key"
        class="mb-3"
      >
        <h3
          class="flex items-center justify-between rounded-t-lg bg-white p-5 text-[19px] text-primary md:justify-start lg:rounded-none md:text-[25px]"
        >
          <span>{{ partner.name }}</span>
          <span class="ml-2 text-sm font-bold text-gray-500"
            >{{ partner.products.length }} produit(s)</span
          >
        </h3>
        <div class="hidden lg:flex lg:w-full lg:flex-row lg:bg-white lg:p-2">
          <div class="flex lg:w-7/12">
            <div class="ml-2">
              <span class="mt-2 text-[14px] text-gray-400"
                >Description de l’article</span
              >
            </div>
          </div>
          <div class="flex lg:w-5/12">
            <div class="mr-5 w-2/12 text-center">
              <span class="mt-2 text-[14px] text-gray-400">Qté</span>
            </div>
            <div class="w-4/12 text-left">
              <span class="mt-2 text-[14px] text-gray-400"
                >Prix unitaire HT</span
              >
            </div>
            <div class="w-4/12 text-left">
              <span class="mt-2 text-[14px] text-gray-400">Sous-total HT</span>
            </div>
            <div class="w-1/12"></div>
          </div>
        </div>
        <ProductRecapComponent
          v-for="(product, proKey) in partner.products"
          :key="proKey"
          :product="product"
          class="border-b-2 border-gray-300 p-2"
        />
        <div
          class="flex w-full flex-row justify-center bg-white p-2 lg:justify-end"
        >
          <div class="hidden lg:ml-2 lg:flex lg:w-8/12"></div>
          <div class="w-4/12 text-center lg:w-2/12">
            <span class="mt-2 text-[14px] text-gray-400"
              >Sous-total fournisseur</span
            >
          </div>
          <div class="w-5/12 text-left lg:w-2/12 lg:text-center">
            <span class="primary mt-2 text-lg font-bold"
              >{{ totalPriceByPartner(partner.products) }}€ HT</span
            >
          </div>
          <div class="hidden lg:flex lg:w-1/12"></div>
        </div>
        <div
          class="float-left flex w-full flex-col rounded-b-lg bg-white p-2 px-3 lg:rounded-none lg:px-6"
        >
          <p class="text-sm text-gray-500 lg:text-lg">
            {{ partner.label_livraison }}
          </p>
          <p
            class="mt-5 flex flex-col text-sm text-gray-500 lg:mt-7 lg:flex-row lg:items-center lg:text-lg"
          >
            <span class="flex">Méthode de livraison:</span>
            <select
              class="flex h-[35px] rounded-md py-0 text-gray-600 placeholder-gray-400 lg:ml-2"
            >
              <option>Options de livraison</option>
            </select>
          </p>
          <p
            class="mt-7 mb-3 flex inline-flex items-start text-sm text-gray-500 lg:items-center lg:text-lg"
          >
            <input
              type="checkbox"
              name="condition_legale"
              class="mr-2 mt-1 lg:mt-0"
            />
            J'accepte les Conditions Générales de Vente du fournisseur
          </p>
        </div>
      </div>
    </template>
    <template #right-side>
      <div class="flex flex-col-reverse lg:flex-col">
        <div
          class="mt-10 flex items-center justify-between md:justify-start lg:justify-between lg:mt-0 lg:mb-4"
        >
          <a
            href="#"
            class="flex text-sm text-secondary md:mr-3 lg:mr-0"
          >
            <FileIconComponent class="mr-1" /> Devis en pdf
          </a>
          <ButtonComponent
            class="flex button-secondary !bg-transparent !text-secondary text-base !px-2 md:!px-8 border border-secondary lg:text-lg"
          >
            <SaveIconComponent class="mr-1 lg:hidden text-lg" /> Sauvegarder le panier
          </ButtonComponent>
        </div>
        <div>
          <CartRightSideComponent :next-url="'/app/cart/adresses'">
            <template #title> Récapitulatif panier </template>
            <template #button-label> Passer la commande </template>
          </CartRightSideComponent>
          <div class="mt-5 flex justify-start">
            <div class="h-14 mr-4 items-center rounded-lg bg-white p-5">
              <CbIconComponent />
            </div>
            <div class="h-14 items-center rounded-lg bg-white p-5">
              <SepaIconComponent />
            </div>
          </div>
        </div>
      </div>
    </template>
  </CartPage>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import FileIconComponent from '@/vuejs/modules/shared/icon/FileIconComponent.vue'
import ProductRecapComponent from '@/vuejs/modules/cart/components/ProductRecapComponent.vue'
import CbIconComponent from '@/vuejs/modules/shared/icon/CbIconComponent.vue'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import {
  productsTopVenteHomepage,
  productsSimilaire,
} from '@/vuejs/modules/products'
import SaveIconComponent from '@/vuejs/modules/shared/icon/SaveIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

const detailsCart = ref({
  company_name: 'Qantis',
  partners: [
    {
      name: 'ALDA MAJUSCULE',
      products: [productsSimilaire[0], productsSimilaire[1]],
      label_livraison:
        'Il vous reste 18,28€ HT de commande pour bénéficier de la livraison gratuite',
    },
    {
      name: 'BERNER',
      products: [productsTopVenteHomepage[1]],
      label_livraison: 'Livraison offerte pour les adhérents QANTIS',
    },
  ],
})

const totalPriceByPartner = (products: Array): Promise<any> => {
  let total = 0
  products.forEach((value, index) => {
    total = parseFloat(value.price.replace(',', '.')) + parseFloat(total)
  })

  return total.toLocaleString().replace('.', ',')
}
</script>

<style scoped></style>
