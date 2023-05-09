<template>
  <div v-if="savedCart" class="bloc-cart-saved">
    <div class="md:w-5/12">
      <RouterLink
        :to="{
          name: PageList.SAVED_CARTS_DETAILS,
          params: { id: savedCart.id },
        }"
        class="text-purple-600 underline"
        >{{ savedCart.name }}
      </RouterLink>
    </div>
    <div class="md:w-2/12">
      {{ createdAd }}
    </div>
    <div class="md:w-3/12">
      {{ nbProducts }}
      {{ nbProducts > 1 ? 'articles' : 'article' }}
    </div>
    <!--    <div class="md:w-2/12">-->
    <!--      &lt;!&ndash;      <span class="primary flex font-bold">{{ cart.total }}€ HT</span>&ndash;&gt;-->
    <!--      &lt;!&ndash;      <span class="flex text-gray-500">({{ cart.total }}€ HT)</span>&ndash;&gt;-->
    <!--    </div>-->
    <div class="flex justify-end md:w-2/12">
      <div class="flex">
        <RouterLink
          :to="{
            name: PageList.SAVED_CARTS_DETAILS,
            params: { id: savedCart.id },
          }"
          class="flex"
        >
          <EyeIconComponent class="mr-2 stroke-secondary" />
        </RouterLink>
        <button class="flex" @click="openAddToCartConfirm">
          <ShoppingCartIconComponent class="mr-2 !stroke-secondary stroke-2" />
        </button>
        <button class="flex" @click="openDeleteForm">
          <TrashIconComponent :stroke-color="'#9866ff'" />
        </button>
      </div>
      <SavedCartModal
        v-if="showForm"
        class="modal"
        :is-loading="isLoading"
        @cancel="showForm = false"
        @submit-saved-cart="onSubmitSavedCart"
      />
      <SavedCartDeleteModal
        v-if="showDeleteForm"
        class="modal"
        :is-loading="isLoading"
        :saved-cart-id="savedCart.id"
        @cancel="showDeleteForm = false"
        @delete-saved-cart="onDelete"
      />
      <SavedCartAddToCartModal
        v-if="showAddToCartConfirm"
        class="modal"
        :is-loading="isLoading"
        :saved-cart-id="savedCart.id"
        @cancel="showAddToCartConfirm = false"
        @add-to-cart="onAddToCart"
      />
    </div>
  </div>
</template>
<script lang="ts" setup>
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import { computed, ref } from 'vue'
import { format } from 'date-fns'
import SavedCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartModal.vue'
import EyeIconComponent from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { PageList } from '@/vuejs/router'
import SavedCartDeleteModal from '@/vuejs/modules/account/components/savedCart/SavedCartDeleteModal.vue'
import SavedCartAddToCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartAddToCartModal.vue'

const props = defineProps({
  savedCart: {
    required: true,
    type: Object,
  },
})

const emit = defineEmits(['submit', 'delete', 'addToCart'])
const showForm = ref<boolean>(false)
const showDeleteForm = ref<boolean>(false)
const showAddToCartConfirm = ref<boolean>(false)
const isLoading = ref<boolean>(false)

const createdAd = computed(() => {
  return format(new Date(props.savedCart.createdAt), 'dd/MM/yyyy')
})

const nbProducts = computed(() => {
  return props.savedCart.savedCartProducts.length
})

const openDeleteForm = () => {
  showDeleteForm.value = true
}

const openAddToCartConfirm = () => {
  showAddToCartConfirm.value = true
}
const onSubmitSavedCart = async (event) => {
  await emit('submit', {
    savedCart: event.savedCart,
  })
  showForm.value = false
}

const onDelete = async (event) => {
  await emit('delete', {
    savedCartId: event.savedCartId,
  })

  showDeleteForm.value = false
}

const onAddToCart = async (event) => {
  await emit('addToCart', {
    savedCartId: event.savedCartId,
  })

  showDeleteForm.value = false
}
</script>
<style scoped>
.bloc-cart-saved {
  @apply mb-2.5 flex w-[48.5%] flex-col rounded-lg bg-white p-2.5 text-sm text-gray-500 md:w-full md:flex-row md:text-base lg:text-lg;
}
</style>
