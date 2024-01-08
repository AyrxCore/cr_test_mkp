<template>
  <div v-if="favorite" class="bloc-item-favoris">
    <div class="md:w-5/12">
      <RouterLink
        :to="{ name: PageList.FAVORITES_DETAILS, params: { id: favorite.id } }"
        class="flex items-center font-bold text-primary underline"
        :title="favorite.public ? 'Liste partagée' : ''"
        @click="sendGaEvent('click_favorites_details')"
      >
        {{ favorite.name }}
        <MultipleUserComponent
          v-if="favorite.public"
          class="ml-2 w-8 fill-secondary"
        />
      </RouterLink>
    </div>
    <div class="md:w-2/12">
      {{ createdAd }}
    </div>
    <div class="md:w-2/12">
      {{ updatedAt }}
    </div>
    <div class="md:w-2/12">
      {{ favorite.nbFavoriteProducts }}
    </div>
    <div class="flex justify-end md:w-1/12">
      <button
        :class="{
          'disabled cursor-not-allowed': !canDelete,
        }"
        @click="openFavoriteForm"
      >
        <EditIconComponent class="mr-2" :stroke="channelPrimaryColor" />
      </button>
      <button
        :class="{
          'disabled cursor-not-allowed': !canDelete,
        }"
        @click="openDeleteFavoriteForm"
      >
        <TrashIconComponent :stroke="channelPrimaryColor" />
      </button>
    </div>
    <FavoriteFormModal
      v-if="canDelete && showFormFavorite"
      class="modal"
      :favorite-id="favorite.id"
      :is-editing="true"
      @cancel="showFormFavorite = false"
      @submit-favorite="onSubmitFavorite"
    />
    <FavoriteDeleteModal
      v-if="canDelete && deleteFavorite"
      class="modal"
      :favorite-id="favorite.id"
      @cancel="deleteFavorite = false"
      @delete-favorite="onDeleteFavorite"
    />
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import { computed, PropType, ref } from 'vue'
import { Favorite } from '@/vuejs/types/Favorite'
import { format } from 'date-fns'
import { PageList } from '@/vuejs/router'
import FavoriteFormModal from '@/vuejs/modules/account/components/favorite/FavoriteAddEditModal.vue'
import FavoriteDeleteModal from '@/vuejs/modules/account/components/favorite/FavoriteDeleteModal.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import MultipleUserComponent from '@/vuejs/modules/shared/icon/MultipleUserComponent.vue'
import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const alertStore = useAlertStore()
const emit = defineEmits(['submitFavorite', 'deleteFavorite'])

const { channelPrimaryColor } = storeToRefs(useChannelStore())

const showFormFavorite = ref<boolean>(false)
const deleteFavorite = ref<boolean>(false)

const props = defineProps({
  favorite: {
    required: true,
    type: Object as PropType<Favorite>,
  },
  canDelete: {
    type: Boolean,
    default: true,
  },
})

const createdAd = computed(() => {
  return format(new Date(props.favorite.createdAt), 'dd/MM/yyyy')
})

const updatedAt = computed(() => {
  return format(new Date(props.favorite.updatedAt), 'dd/MM/yyyy')
})

const openFavoriteForm = () => {
  showFormFavorite.value = true
  sendGaEvent('click_favorites_edit')
}

const openDeleteFavoriteForm = () => {
  deleteFavorite.value = true
  sendGaEvent('click_favorites_delete')
}
const onSubmitFavorite = async (event) => {
  await emit('submitFavorite', {
    favorite: event.favorite,
    isEditing: event.isEditing,
  })
  showFormFavorite.value = false
}

const onDeleteFavorite = async (event) => {
  if (props.canDelete) {
    await emit('deleteFavorite', {
      favoriteId: event.favoriteId,
      selectedFavoriteId: event.selectedFavoriteId,
    })
  } else {
    alertStore.setShow(
      `Vous ne pouvez pas supprimer le favori <strong>${props.favorite.name}</strong>`,
      AlertType.warning,
    )
  }

  deleteFavorite.value = false
}
</script>
<style scoped>
.bloc-item-favoris {
  @apply mb-4 flex w-[48.5%] flex-col rounded-lg bg-white p-2.5 text-sm md:w-full md:flex-row md:text-base lg:text-lg;
}
</style>
