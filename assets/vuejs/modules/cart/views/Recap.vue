<template>
  <CartPage >
    <template #left-side>
      <div class="">
        <h3 class="primary mb-2 text-[35px]">Panier <span class="uppercase">{{ detailsCart.company_name }}</span></h3>
      </div>
      <div v-for="(partner, key) in detailsCart.partners" :key="key" class="mb-3">
        <h3 class="primary bg-white p-2 text-[25px] items-center">{{ partner.name }}
          <span class="text-gray-500 text-sm font-bold ml-2">{{partner.products.length}} produit(s)</span>
        </h3>
        <div class="flex p-2 w-full flex-row bg-white">
          <div class="w-[60%] ml-2 mr-2">
            <span class="text-gray-400 mt-2 text-[14px]">Description de l’article</span>
          </div>
          <div class="w-[5%] text-center mr-14">
            <span class="text-gray-400 mt-2 text-[14px]">Qté</span>
          </div>
          <div class="w-[10%] mr-4">
            <span class="text-gray-400 mt-2 text-[14px]">Prix unitaire HT</span>
          </div>
          <div class="w-[10%]">
            <span class="text-gray-400 mt-2 text-[14px]">Sous-total HT</span>
          </div>
        </div>
        <ProductRecapComponent
          v-for="(product, proKey) in partner.products"
          :key="proKey"
          :product="product"
          class="border-b-2 border-gray-300 p-2"
        />
        <div class="flex p-2 w-full flex-row bg-white">
          <div class="w-[80%] text-right ml-2 mr-10">
            <span class="text-gray-400 mt-2 text-[14px]">Sous-total fournisseur</span>
          </div>
          <div class="w-[10%]">
            <span class="primary font-bold text-lg mt-2">XX€ HT</span>
          </div>
        </div>
        <div class="p-2 w-full flex-row bg-white px-6">
          <p class="text-gray-500 text-lg">
            Il vous reste XX€ HT de commande pour bénéficier de la livraison gratuite
          </p>
          <p class="text-gray-500  text-lg inline-flex w-full items-center mt-7">
            Méthode de livraison:
            <select
              class="h-[35px] ml-2 rounded-md text-gray-600 placeholder-gray-400 py-0"
            >
              <option>Options de livraison</option>
            </select>
          </p>
          <p class="text-gray-500 inline-flex items-center mt-7 text-lg mb-3">
            <input type="checkbox" name="condition_legale" class="mr-2" />
            J'accepte les Conditions Générales de Vente du fournisseur
          </p>
        </div>
      </div>
    </template>
    <template #right-side>
      <div class="inline-flex items-center mb-2">
        <a href="#" class="inline-flex text-purple-600 mr-3"><FileIconComponent class="mr-2"/> Devis en pdf </a>
        <DefaultButtonComponent
          :btn-text-color="'text-purple-500'"
          :btn-color="'bg-transparent'"
          :rounded="'rounded-full'"
          class="border border-purple-600"
        >
          Sauvegarder le panier
        </DefaultButtonComponent>
      </div>
      <CartRightSideComponent>
        <template #title>
          Récapitulatif panier
        </template>
        <template #button-label>
          Passer la commande
        </template>
      </CartRightSideComponent>
      <div class="grid grid-cols-4 gap-x-16 mt-5">
        <div class="p-5 bg-white rounded-lg items-center m-auto h-14">
          <CbIconComponent />
        </div>
        <div class="p-5 bg-white rounded-lg items-center m-auto h-14">
          <SepaIconComponent />
        </div>
      </div>
    </template>
  </CartPage>
</template>
<script lang="ts" setup>

import {ref} from 'vue'
import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import FileIconComponent from '@/vuejs/modules/shared/icon/FileIconComponent.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import productImage from '@/vuejs/assets/img/default-image.png'
import { getImage } from '@/vuejs/services/utils'
import ProductRecapComponent from '@/vuejs/modules/cart/components/ProductRecapComponent.vue'
import CbIconComponent from '@/vuejs/modules/shared/icon/CbIconComponent.vue'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'

const productImageFile = getImage(productImage)

const detailsCart = ref({
  company_name: 'Qantis',
  partners: [
    {
      name: 'Partenaire N°1',
      products: [
        {
          name: 'Description du produit ou du service N°1',
          reference: 'XXXXXXXXXXX',
          seller: 'XXXXXXXXXX',
          qte: '3',
          price_ht: 'XX',
          price_ttc: 'XX',
          img: productImageFile
        },
        {
          name: 'Description du produit ou du service N°2',
          reference: 'XXXXXXXXXXX',
          seller: 'XXXXXXXXXX',
          qte: '3',
          price_ht: 'XX',
          price_ttc: 'XX',
          img: productImageFile
        }
      ]
    },
    {
      name: 'Partenaire N°2',
      products: [
        {
          name: 'Description du produit ou du service N°3',
          reference: 'XXXXXXXXXXX',
          seller: 'XXXXXXXXXX',
          qte: '3',
          price_ht: 'XX',
          price_ttc: 'XX',
          img: productImageFile
        }
      ]
    }
  ]
})

</script>

<style scoped></style>
