<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg text-gray-500 md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="mr-2">
        <input
          v-if="product"
          v-model="selectedProduct"
          type="checkbox"
          class="checkbox-secondary"
          @change="onSelectProduct"
        />
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
          :to="{ name: PageList.PRODUCT, params: { slug: productSlug } }"
          class="text-lg font-bold text-primary lg:text-[22px]"
        >
          {{ productName }}
        </RouterLink>
        <span class="flex flex-col text-sm text-gray-500 lg:text-lg">
          <span>Vendu par: {{ productSeller }}</span>
          <span>Référence: {{ productReference }}</span>
        </span>
        <span class="mt-2 flex text-sm text-green-400 lg:text-lg"
          >En stock</span
        >
      </div>
    </div>
    <div class="md:w-4/12 lg:w-3/12">
      <div class="md:justify-end">
        <div
          class="flex w-full flex-row flex-wrap items-center items-center justify-between md:w-auto"
        >
          <span
            class="flex items-start items-center text-sm font-bold text-primary md:text-base lg:text-lg"
          >
            {{ productPrice }}€ HT
          </span>
          <div class="bottom-0 flex items-start justify-between space-x-3">
            <button @click="openMoveProductForm">
              <ChangeIconComponent :stroke-color="'#9866ff'" />
            </button>
            <button @click="openRemoveForm">
              <TrashIconComponent :stroke-color="'#9866ff'" />
            </button>
          </div>
          <ProductDeleteModal
            v-if="removeProduct"
            class="modal"
            :favorite-product="favoriteProduct"
            :favorite-id="favoriteId"
            @cancel="removeProduct = false"
            @remove-product="onRemoveProduct"
          />

          <ProductMoveModal
            v-if="moveProduct"
            class="modal"
            :favorite-product="favoriteProduct"
            :favorite-id="favoriteId"
            @cancel="moveProduct = false"
            @move-product="onMoveProduct"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import { computed, onMounted, PropType, ref } from 'vue'
import { Product } from '@/vuejs/types/Product'
import { useProductStore } from '@/vuejs/stores/product'
import { PageList } from '@/vuejs/router'
import ChangeIconComponent from '@/vuejs/modules/shared/icon/ChangeIconComponent.vue'
import ProductDeleteModal from '@/vuejs/modules/account/components/favorite/ProductRemoveModal.vue'
import ProductMoveModal from '@/vuejs/modules/account/components/favorite/ProductMoveModal.vue'
import { FavoriteProduct } from '@/vuejs/types/Favorite'

const emit = defineEmits([
  'removeProduct',
  'moveProduct',
  'selectedProduct',
  'removeSelectedProduct',
])

const productStore = useProductStore()
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

const onSelectProduct = async () => {
  if (selectedProduct.value) {
    await emit('selectedProduct', {
      selectedProduct: props.favoriteProduct,
      product: product.value,
    })
  } else {
    await emit('removeSelectedProduct', {
      selectedProduct: props.favoriteProduct,
    })
  }
}

const onRemoveProduct = async (event) => {
  await emit('removeProduct', {
    favoriteProductId: event.favoriteProductId,
  })
  removeProduct.value = false
}

const onMoveProduct = async (event) => {
  await emit('moveProduct', {
    favoriteId: event.favoriteId,
    favoriteProductId: event.favoriteProductId,
  })
  moveProduct.value = false
}

onMounted(async (): Promise<void> => {
  product.value = await productStore.initProduct(
    props.favoriteProduct.upplerProductId,
  )
  if (!product.value) {
    productNotFound.value = true
  } else {
    priceReference.value = product.value.priceReference
    price.value = product.value.price
    percent.value = product.value.percent
  }
})

const productImage = computed((): string => {
  return product.value ? product.value.images[0] : null
})

const productSlug = computed((): string => {
  return product.value
    ? product.value.slug
    : props.favoriteProduct.upplerProductId
})

const productName = computed((): string => {
  return product.value
    ? product.value.name
    : props.favoriteProduct.upplerProductName
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
