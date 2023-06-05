<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg text-gray-500 md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="mr-2">
        <input
          v-if="productData"
          v-model="selectedItem"
          type="checkbox"
          class="cursor-pointer appearance-none rounded border border-gray-400 text-secondary checked:bg-secondary focus:ring-secondary"
          @change="onSelectItem"
        />
      </div>
      <div class="flex w-6/12 md:w-3/12">
        <img
          :src="productImage"
          alt="Image produit"
          class="flex h-full w-full max-w-max cursor-pointer items-center"
        />
      </div>
      <div class="flex w-6/12 flex-col md:ml-5 md:w-7/12">
        <RouterLink
          :to="{ name: PageList.PRODUCT, params: { id: productId } }"
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
            <button @click="openMoveItemForm">
              <ChangeIconComponent :stroke-color="'#9866ff'" />
            </button>
            <button @click="openRemoveForm">
              <TrashIconComponent :stroke-color="'#9866ff'" />
            </button>
          </div>
          <ItemDeleteModal
            v-if="removeItem"
            class="modal"
            :product="product"
            :favorite-id="favoriteId"
            @cancel="removeItem = false"
            @remove-item="onRemoveItem"
          />

          <ItemMoveModal
            v-if="moveItem"
            class="modal"
            :product="product"
            :favorite-id="favoriteId"
            @cancel="moveItem = false"
            @move-item="onMoveItem"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import { computed, onMounted, ref } from 'vue'
import { getImage } from '@/vuejs/services/utils'
import sampleImg from '@/vuejs/assets/img/sample_product_img.png'
import { Product } from '@/vuejs/types/Product'
import { useProductStore } from '@/vuejs/stores/product'
import { PageList } from '@/vuejs/router'
import ChangeIconComponent from '@/vuejs/modules/shared/icon/ChangeIconComponent.vue'
import ItemDeleteModal from '@/vuejs/modules/account/components/favorite/ItemRemoveModal.vue'
import ItemMoveModal from '@/vuejs/modules/account/components/favorite/ItemMoveModal.vue'

const emit = defineEmits([
  'removeItem',
  'moveItem',
  'selectedItem',
  'removeSelectedItem',
])
const productStore = useProductStore()
const productData = ref<Product>()
const productNotFound = ref(false)
const selectedItem = ref(null)
const priceReference = ref()
const price = ref()
const percent = ref()
const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
})
const removeItem = ref<boolean>(false)
const moveItem = ref<boolean>(false)

const openRemoveForm = () => {
  removeItem.value = true
}

const openMoveItemForm = () => {
  moveItem.value = true
}

const onSelectItem = async () => {
  if (selectedItem.value) {
    await emit('selectedItem', {
      selectedItem: props.product,
      product: productData.value,
    })
  } else {
    await emit('removeSelectedItem', {
      selectedItem: props.product,
    })
  }
}

const onRemoveItem = async (event) => {
  await emit('removeItem', {
    favoriteId: event.favoriteId,
    productId: event.productId,
    variantId: event.variantId,
  })
  removeItem.value = false
}

const onMoveItem = async (event) => {
  await emit('moveItem', {
    favoriteId: event.favoriteId,
    favoriteIdToReceive: event.favoriteIdToReceive,
    favoriteProductId: event.favoriteProductId,
  })
  moveItem.value = false
}

onMounted(async (): Promise<void> => {
  productData.value = await productStore.findProductById(
    props.product.upplerProductId,
  )
  if (!productData.value) {
    productNotFound.value = true
  } else {
    priceReference.value = productData.value.priceReference
    price.value = productData.value.price
    percent.value = productData.value.percent
  }
})

const productImage = computed((): string => {
  if (productData.value) return productData.value.images[0]
  return getImage(sampleImg)
})

const productId = computed((): string => {
  return props.product.upplerProductId
})

const productName = computed((): string => {
  return productData.value
    ? productData.value.name
    : props.product.upplerProductName
})

const productReference = computed((): string => {
  return productData.value ? productData.value.reference : ''
})

const productPrice = computed((): number | string => {
  return productData.value ? productData.value.price : ''
})

const productSeller = computed((): string => {
  return productData.value ? productData.value.seller.name : ''
})
</script>
<style scoped>
.input-qte {
  @apply rounded-lg border border-gray-300 px-0 text-center text-sm md:text-base lg:text-lg;
}
</style>
