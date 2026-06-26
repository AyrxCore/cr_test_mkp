<template>
  <div v-if="hasOptions" class="my-4">
    <div class="mb-2 text-lg font-bold text-primary md:text-xl">
      Mes options
    </div>
    <div
      v-for="(optionData, optionName) in sortedOptions"
      :key="optionName"
      class="mt-2 flex w-full items-center justify-between bg-white px-4 py-2"
    >
      <span class="text-sm md:text-base lg:text-lg">
        {{ optionName }}
      </span>
      <select
        v-model="selectedOptions[optionName]"
        class="h-[1.75rem] w-1/2 border-none p-0"
        @change="updateProductVariant"
      >
        <option
          v-for="(value, index) in optionData.values"
          :key="index"
          :value="getOptionValue(value as OptionValue)"
        >
          {{ getOptionLabel(value as OptionValue) }}
        </option>
      </select>
    </div>
  </div>

  <div
    v-if="variantNotAvailable"
    class="my-3 rounded border border-orange-400 bg-orange-50 p-3 text-sm text-orange-800"
  >
    <div class="font-semibold">Combinaison non disponible</div>
    <div class="mt-1">
      Cette combinaison d'options n'est pas disponible. Veuillez modifier votre
      sélection.
    </div>
  </div>

  <div class="flex justify-between md:flex-col">
    <div class="lg:my-6">
      <div class="relative inline-flex items-center">
        <span class="mr-2 hidden md:block"> Quantité </span>
        <ProductQuantityComponent
          :quantity="productState?.quantity"
          :min-quantity="productState?.minOrderQuantity ?? 1"
          :max-quantity="productState?.maxOrderQuantity ?? 999"
          @update-quantity="updateQuantity"
          @update-quantity-input="updateQuantityInput"
        />
      </div>
    </div>
    <LoaderSharedComponent
      v-if="isLoadingPrice"
      class="text-secondary"
      classes="loader-lg loader"
    />
    <div v-else class="mb-4 flex flex-col items-start">
      <div class="flex items-end">
        <div
          v-if="displayPrice !== null"
          class="mr-2 text-xl font-bold text-primary md:text-3xl"
        >
          {{ formatPrice(displayPrice) }}€ HT
        </div>
        <div
          v-if="
            displayPriceReference !== null &&
            displayPriceReference !== displayPrice
          "
          :class="{
            'text-sm text-gray-500 line-through md:text-base lg:text-lg':
              displayPrice !== null,
            'text-xl font-bold text-primary': displayPrice === null,
          }"
        >
          {{ formatPrice(displayPriceReference) }}€ HT
        </div>
      </div>
      <div
        v-if="productState.ecoTax"
        class="mt-1 rounded py-0.5 text-xs text-gray-700"
      >
        dont {{ formatPrice(productState.ecoTax) }}€ d'éco-part
      </div>
    </div>
  </div>
  <div v-if="displayShippingInfo" class="mt-2">
    <h4 class="text-lg md:text-xl">Infos livraison</h4>
    <div class="mt-2 flex items-center">
      <TruckIconComponent class="mr-4 w-8 shrink-0 md:w-6" />
      <span v-if="hasShippingInfo" class="text-sm md:text-base">
        {{ shippingInfoText }}
      </span>
      <span v-else>
        {{ productState.seller.description }}
      </span>
    </div>
  </div>
  <ProductAddToCartComponent
    :product="productState"
    :disabled="variantNotAvailable"
    class="mt-4 hidden lg:flex"
  />
</template>

<script lang="ts" setup>
import { computed, PropType, ref, watch } from 'vue'

import { Product } from '@/vuejs/types/Product'

import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ProductAddToCartComponent from '@/vuejs/modules/products/components/ProductAddToCartComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
import TruckIconComponent from '@/vuejs/modules/shared/icon/TruckIconComponent.vue'

import {
  areValuesEqual,
  getOptionLabel,
  getOptionValue,
  parseOptionValue,
  sortOptionValues,
  variantOptionsToSelectedOptions,
  type OptionValue,
} from '@/vuejs/modules/products/utils/option-utils'

interface QuantityEvent {
  quantity: number
}

interface ProductOptionData {
  type?: string
  values: OptionValue[]
}

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits<{
  'update:product': [product: Product]
}>()

const productState = ref<Product>(props.product)
const selectedOptions = ref<Record<string, string>>({})
const isLoadingPrice = ref<boolean>(false)
const variantNotAvailable = ref<boolean>(false)
const isInitialized = ref<boolean>(false)

const displayPrice = computed<number | null>(() => {
  const value = productState.value?.price
  return typeof value === 'number' ? value : null
})

const displayPriceReference = computed<number | null>(() => {
  const value = productState.value?.priceReference
  return typeof value === 'number' ? value : null
})

const hasOptions = computed<boolean>(() => {
  const options = productState.value?.options
  return (
    options && typeof options === 'object' && Object.keys(options).length > 0
  )
})

const sortedOptions = computed<Record<string, ProductOptionData>>(() => {
  const rawOptions =
    (productState.value?.options as Record<string, ProductOptionData>) ?? {}

  return Object.entries(rawOptions).reduce<Record<string, ProductOptionData>>(
    (accumulator, [optionName, optionData]) => {
      const values = Array.isArray(optionData?.values) ? optionData.values : []

      accumulator[optionName] = {
        ...optionData,
        values: sortOptionValues(values, optionData?.type),
      }

      return accumulator
    },
    {},
  )
})

const hasShippingInfo = computed<boolean>(
  () => !!productState.value?.seller?.supplierDeliveryInfo,
)

const displayShippingInfo = computed<boolean>(
  () => hasShippingInfo.value || !!productState.value?.seller?.description,
)

const shippingInfoText = computed<string>(() => {
  return productState.value?.seller?.supplierDeliveryInfo ?? ''
})

const formatPrice = (price: number): string =>
  Number.isInteger(price) ? price.toString() : price.toFixed(2)

const updateQuantity = ({ quantity }: QuantityEvent) => {
  productState.value = { ...productState.value, quantity }
}

const updateQuantityInput = ({ quantity }: QuantityEvent) => {
  productState.value = { ...productState.value, quantity }
}

const updateProductVariant = () => {
  // Matching côté frontend - pas d'appel API
  const matchingVariant = productState.value.variants?.find((variant) => {
    // Vérifier que toutes les options sélectionnées correspondent au variant
    return Object.entries(selectedOptions.value).every(
      ([optionName, selectedValue]) => {
        const variantValue = variant.options?.[optionName]
        const parsedSelectedValue = parseOptionValue(selectedValue)
        return areValuesEqual(variantValue, parsedSelectedValue)
      },
    )
  })

  if (matchingVariant) {
    // Variant trouvé : tout est OK
    variantNotAvailable.value = false

    // Mettre à jour le produit avec les données du variant trouvé
    const updatedProduct = {
      ...productState.value,
      price: matchingVariant.price,
      priceReference: matchingVariant.priceReference,
      percent: matchingVariant.percent,
      reference: matchingVariant.externalId,
      offerPriceExternalId: matchingVariant.offerPriceExternalId,
      images:
        matchingVariant.images && matchingVariant.images.length > 0
          ? matchingVariant.images
          : productState.value.images,
    }

    productState.value = updatedProduct
    emit('update:product', updatedProduct)
  } else {
    // Aucun variant ne correspond : afficher le message d'avertissement
    variantNotAvailable.value = true
    // eslint-disable-next-line no-console
    console.warn(
      'No matching variant found for selected options:',
      selectedOptions.value,
    )
  }
}

watch(
  () => props.product,
  (newProduct, oldProduct) => {
    if (isInitialized.value && newProduct?.id === oldProduct?.id) {
      return
    }

    productState.value = newProduct

    // Initialiser les options sélectionnées avec celles du variant par défaut
    if (newProduct?.defaultVariantId && newProduct?.variants) {
      const defaultVariant = newProduct.variants.find(
        (v) => v.id === newProduct.defaultVariantId,
      )
      if (defaultVariant?.options) {
        selectedOptions.value = variantOptionsToSelectedOptions(
          defaultVariant.options,
        )

        // Appliquer immédiatement les données du variant par défaut (images, prix)
        const updatedProduct = {
          ...newProduct,
          price: defaultVariant.price ?? newProduct.price,
          priceReference:
            defaultVariant.priceReference ?? newProduct.priceReference,
          percent: defaultVariant.percent ?? newProduct.percent,
          reference: defaultVariant.externalId ?? newProduct.reference,
          offerPriceExternalId:
            defaultVariant.offerPriceExternalId ??
            newProduct.offerPriceExternalId,
          images:
            defaultVariant.images && defaultVariant.images.length > 0
              ? defaultVariant.images
              : newProduct.images,
        }

        productState.value = updatedProduct
        emit('update:product', updatedProduct)
        isInitialized.value = true
      }
    } else {
      isInitialized.value = true
    }
  },
  { immediate: true },
)
</script>
