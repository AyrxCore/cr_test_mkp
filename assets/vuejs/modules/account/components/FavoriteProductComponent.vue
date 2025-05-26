<template>
  <div v-if="favorite" class="bloc-item-favoris">
    <div class="md:w-5/12">
      <RouterLink
        :title="favorite.public ? 'Liste partagée' : ''"
        :to="{ name: PageList.FAVORITES_DETAILS, params: { id: favorite.id } }"
        class="flex items-center font-bold text-primary underline"
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
      <VTooltip :triggers="['hover', 'focus']" class="flex">
        <button
          :class="{
            disabled: !canDelete || isNeoAutoLogin,
          }"
          @click="openFavoriteForm"
        >
          <EditIconComponent :stroke="channelPrimaryColor" class="mr-2" />
        </button>
        <template #popper> Editer une liste de favoris</template>
      </VTooltip>
      <VTooltip :triggers="['hover', 'focus']" class="flex">
        <button
          :class="{
            disabled: !canDelete || isNeoAutoLogin,
          }"
          @click="openDeleteFavoriteForm"
        >
          <TrashIconComponent :stroke="channelPrimaryColor" />
        </button>
        <template #popper> Supprimer une liste de favoris</template>
      </VTooltip>
    </div>
    <FavoriteFormModal
      v-if="showFormEditFavorite"
      :error-submit="errorSubmit"
      :favorite-id="favorite.id"
      :is-editing="true"
      class="modal"
      @cancel="showFormEditFavorite = false"
      @submit-favorite="onSubmitFavorite"
      @change-value="$emit('changeValue')"
    />
    <FavoriteDeleteModal
      v-if="canDelete && deleteFavorite"
      :favorite-id="favorite.id"
      class="modal"
      @cancel="deleteFavorite = false"
      @delete-favorite="onDeleteFavorite"
    />
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import { format } from 'date-fns'
import { storeToRefs } from 'pinia'
import { PageList } from '@/vuejs/router'
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import FavoriteFormModal from '@/vuejs/modules/account/components/favorite/FavoriteAddEditModal.vue'
import FavoriteDeleteModal from '@/vuejs/modules/account/components/favorite/FavoriteDeleteModal.vue'
import MultipleUserComponent from '@/vuejs/modules/shared/icon/MultipleUserComponent.vue'
import { AlertType } from '@/vuejs/types/Alert'
import { Favorite } from '@/vuejs/types/Favorite'
import { useAlertStore } from '@/vuejs/stores/alert'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const emit = defineEmits(['submitFavorite', 'deleteFavorite', 'changeValue'])

const alertStore = useAlertStore()
const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { channelPrimaryColor } = storeToRefs(useChannelStore())

const showFormEditFavorite = ref<boolean>(false)
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
  errorSubmit: {
    type: String,
    required: false,
    default: null,
  },
})

const createdAd = computed(() => {
  return format(new Date(props.favorite.createdAt), 'dd/MM/yyyy')
})

const updatedAt = computed(() => {
  return format(new Date(props.favorite.updatedAt), 'dd/MM/yyyy')
})

const openFavoriteForm = () => {
  showFormEditFavorite.value = true
  sendGaEvent('click_favorites_edit')
}

const openDeleteFavoriteForm = () => {
  deleteFavorite.value = true
  sendGaEvent('click_favorites_delete')
}
const onSubmitFavorite = async (event) => {
  emit('submitFavorite', {
    newFavoriteName: event.newFavoriteName,
    favorite: event.favorite,
    isEditing: event.isEditing,
  })
}

const onDeleteFavorite = async (event) => {
  if (props.canDelete) {
    emit('deleteFavorite', {
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
