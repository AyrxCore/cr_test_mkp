<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="mr-2">
        <div v-if="product" class="w-4">
          <input
            v-if="productStore.isSellable(product)"
            v-model="selectedProduct"
            :disabled="isNeoAutoLogin"
            class="checkbox-secondary"
            type="checkbox"
            @change="onSelectProduct"
          />
        </div>
      </div>
      <div class="flex w-6/12 md:w-3/12">
        <img
          v-if="productImage"
          :src="productImage"
          alt="Image produit"
          class="flex h-full w-full max-w-max cursor-pointer items-center"
        />

        <div
          v-else
          class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
        ></div>
      </div>
      <div class="flex w-6/12 flex-col md:ml-5 md:w-7/12">
        <RouterLink
          v-if="product && !productStore.isAccordCadre(product)"
          :to="{ name: PageList.PRODUCT, params: { slug: productSlug } }"
          class="mt-4 text-lg font-bold text-primary lg:text-2xl"
        >
          {{ productName }}
        </RouterLink>
        <RouterLink
          v-else-if="product && productStore.isAccordCadre(product)"
          :to="{ name: PageList.ACCORD_CADRE, params: { slug: productSlug } }"
          class="mt-4 text-lg font-bold text-primary lg:text-2xl"
        >
          {{ productName }}
        </RouterLink>
        <div
            v-if="product && productStore.isNotSellable(product)"
          class="mt-2 w-fit rounded-sm bg-secondary px-2 py-1 text-white"
        >
          -{{ product?.percent }}% sur le tarif public
        </div>
        <span class="mt-4 flex flex-col text-sm lg:text-lg">
          <span>Vendu par : {{ productSeller }}</span>
          <span v-if="product && !productStore.isAccordCadre(product)"
            >Référence: {{ productReference }}</span
          >
        </span>
      </div>
    </div>
    <div class="md:w-4/12 lg:w-3/12">
      <div class="md:justify-end">
        <div
          class="flex w-full flex-row flex-wrap items-center justify-between md:w-auto"
        >
          <div class="flex items-center">
            <span
              v-if="product && productStore.isSellable(product)"
              class="mt-4 text-sm font-bold text-primary md:text-base lg:text-xl"
            >
              {{ productPrice }}€ HT
            </span>
          </div>
          <div class="bottom-0 flex items-start justify-between space-x-3">
            <button :disabled="isNeoAutoLogin" @click="openMoveProductForm">
              <ChangeIconComponent :fill="channelPrimaryColor" />
            </button>
            <button :disabled="isNeoAutoLogin" @click="openRemoveForm">
              <TrashIconComponent :stroke="channelPrimaryColor" />
            </button>
          </div>
          <ProductDeleteModal
            v-if="removeProduct"
            :favorite-id="favoriteId"
            :favorite-product="favoriteProduct"
            class="modal"
            @cancel="removeProduct = false"
            @remove-product="onRemoveProduct"
          />
          <ProductMoveModal
            v-if="moveProduct"
            :favorite-id="favoriteId"
            :favorite-product="favoriteProduct"
            class="modal"
            @cancel="moveProduct = false"
            @move-product="onMoveProduct"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { PageList } from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useProductStore } from '@/vuejs/stores/product'
import { useUserStore } from '@/vuejs/stores/user'
import { FavoriteProduct } from '@/vuejs/types/Favorite'
import { Product } from '@/vuejs/types/Product'

import ProductDeleteModal from '@/vuejs/modules/account/components/favorite/ProductRemoveModal.vue'
import ProductMoveModal from '@/vuejs/modules/account/components/favorite/ProductMoveModal.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import ChangeIconComponent from '@/vuejs/modules/shared/icon/ChangeIconComponent.vue'

const emit = defineEmits([
  'removeProduct',
  'moveProduct',
  'selectedProduct',
  'removeSelectedProduct',
])

const productStore = useProductStore()
const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { channelPrimaryColor } = storeToRefs(useChannelStore())

const product = ref<Product>()
const productNotFound = ref(false)
const selectedProduct = ref(null)
const priceReference = ref()
const price = ref()
const percent = ref()
const removeProduct = ref<boolean>(false)
const moveProduct = ref<boolean>(false)

const props = defineProps({
  favoriteProduct: {
    required: true,
    type: Object as PropType<FavoriteProduct>,
  },
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
})

const openRemoveForm = () => {
  removeProduct.value = true
}

const openMoveProductForm = () => {
  moveProduct.value = true
}

const onSelectProduct = () => {
  if (selectedProduct.value) {
    emit('selectedProduct', {
      selectedProduct: props.favoriteProduct,
      product: product.value,
    })
  } else {
    emit('removeSelectedProduct', {
      selectedProduct: props.favoriteProduct,
    })
  }
}

const onRemoveProduct = (event) => {
  emit('removeProduct', {
    favoriteProductId: event.favoriteProductId,
  })
  removeProduct.value = false
}

const onMoveProduct = (event) => {
  emit('moveProduct', {
    favoriteId: event.favoriteId,
    favoriteProductId: event.favoriteProductId,
  })
  moveProduct.value = false
}

onMounted(async (): Promise<void> => {
  // product.value = await productStore.initProduct(
  //   props.favoriteProduct.upplerProductId,
  // )
  // if (!product.value) {
  //   productNotFound.value = true
  // } else {
  //   priceReference.value = product.value.priceReference
  //   price.value = product.value.price
  //   percent.value = product.value.percent
  // }
})

const productImage = computed((): string => {
  if (product.value && productStore.isAccordCadre(product.value)) {
    return product.value.properties['logo_partenaire']
  }
  return product.value?.images[0]
})

const productSlug = computed(() => {
  // return product.value
  //   ? product.value.slug
  //   : props.favoriteProduct.upplerProductId
})

const productName = computed(() => {
  // return product.value
  //   ? product.value.name
  //   : props.favoriteProduct.upplerProductName
})

const productReference = computed((): string => {
  return product.value ? product.value.reference : ''
})

const productPrice = computed((): number | string => {
  return product.value ? product.value.price : ''
})

const productSeller = computed((): string => {
  return product.value ? product.value.seller.name : ''
})

</script>

<style scoped>
.input-qte {
  @apply rounded-lg border border-gray-300 px-0 text-center text-sm md:text-base lg:text-lg;
}
</style>
