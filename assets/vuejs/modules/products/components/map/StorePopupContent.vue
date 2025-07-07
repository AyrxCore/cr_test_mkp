<template>
  <div class="m-2 w-56 cursor-auto overflow-hidden break-words md:m-4 md:w-64">
    <div class="my-2 flex flex-col gap-1">
      <div class="flex justify-start">
        <slot name="logo">
          <img
            v-if="store.logo"
            :src="store.logo"
            :alt="store.name"
            class="h-12 w-auto object-contain"
          />
        </slot>
      </div>

      <div class="w-full">
        <slot name="name">
          <strong
            class="block text-sm font-bold uppercase leading-tight text-slate-900"
          >
            {{ store.name }}
          </strong>
        </slot>
      </div>
    </div>

    <slot name="address">
      <p
        class="word-break-break-all mt-1 hyphens-auto break-words text-xs leading-snug text-slate-600"
      >
        {{ store.address }}
      </p>
    </slot>

    <div class="mt-2">
      <div
        class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between md:gap-1"
      >
        <slot name="phone">
          <a
            v-if="store.phone"
            :href="phoneAction.href"
            :class="phoneAction.class"
            @click.stop
          >
            {{ phoneAction.text }}
          </a>
        </slot>
        <slot name="link">
          <RouterLink
            v-if="store.upplerId"
            :to="sellerAction.to"
            target="_blank"
            :class="sellerAction.class"
            @click.stop
          >
            {{ sellerAction.text }}
          </RouterLink>
        </slot>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType, computed } from 'vue'
import { StoreData } from '@/vuejs/types/Seller'
import { ProductPageList } from '@/vuejs/router/pages-list'

const props = defineProps({
  store: {
    type: Object as PropType<StoreData>,
    required: true,
  },
})

const phoneAction = computed(() => ({
  href: `tel:${props.store.phone}`,
  text: props.store.phone,
  class: 'block text-left text-sm font-medium text-blue-500 md:text-xs',
}))

const sellerAction = computed(() => ({
  to: {
    name: ProductPageList.PRODUCTS,
    query: { company: props.store.upplerId },
  },
  text: "Voir l'offre",
  class:
    'mx-auto max-h-7 w-full max-w-[90%] rounded-full bg-primary !px-1 !py-0.5 text-center !text-xs !text-white hover:!scale-100 hover:!transform-none md:mx-0 md:w-auto md:max-w-32 md:!px-3 md:!py-1 md:!text-sm',
}))
</script>
