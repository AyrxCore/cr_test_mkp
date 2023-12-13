<template>
  <div
    ref="wrapper"
    class="rad-[15px] flex w-full rounded-[15px] border-[1px] border-solid border-[#898585]"
  >
    <input
      v-model="searchTerm"
      class="input truncate !rounded-l-[15px] !px-8 !py-4 !text-sm !ring-transparent focus:!rounded-r-[0px] md:text-clip md:!text-lg"
      name="search"
      placeholder="De quoi avez-vous besoin ?"
      type="search"
      @keyup.enter="searchProduct"
      @focus="gtmEvent('focus_header_search_bar')"
    />
    <button
      class="flex items-center rounded-r-[15px] bg-white px-5 py-1 hover:opacity-75"
      @click="searchProduct"
    >
      <SearchIconComponent />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

import SearchIconComponent from '@/vuejs/modules/shared/icon/SearchIconComponent.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'

const userStore = useUserStore()
const channelStore = useChannelStore()

const route = useRoute()
const emit = defineEmits(['searchProduct'])
const wrapper = ref<HTMLElement>()

const searchTerm = ref('')

const currentChannel = channelStore.currentChannel

const searchProduct = () => {
  emit('searchProduct', { term: searchTerm.value })
  gtmEvent('action_header_search', { search_value: searchTerm.value })
}

onMounted(() => {
  updateSearchTerm(route.query.q)
})

const updateSearchTerm = (query) => {
  if (query && route.name === ProductPageList.PRODUCTS) {
    searchTerm.value = query
  } else {
    searchTerm.value = null
  }
}

const gtmEvent = (eventName: string, additionalData = null) => {
  let data = buildStandardGtmData(userStore.user['@id'], currentChannel.name)
  data = additionalData ? { ...data, ...additionalData } : data
  gtmMixinPushEvent(eventName, data)
}

watch(
  () => route.query,
  async (routeObject) => {
    updateSearchTerm(routeObject.q)
  },

  { immediate: true },
)
</script>

<style scoped>
input:placeholder-shown {
  font-style: italic;
}
input::placeholder {
  font-size: 18px;
  color: #bebbbb;
}
</style>
