import { ref, computed, watch, onUnmounted } from 'vue'
// TODO (MKP-1411): Réimporter onBeforeMount quand les Favoris seront disponibles via DJUST
// import { ref, computed, watch, onBeforeMount, onUnmounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useProductStore } from '@/vuejs/stores/product'
// TODO (MKP-1411): Import temporairement commenté - à rétablir quand les Favoris seront disponibles via DJUST
// import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useUserStore } from '@/vuejs/stores/user'
import { useBannerSearchStore } from '@/vuejs/stores/bannerSearch'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useSellerStore } from '@/vuejs/stores/seller'
import { formatProductGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { getImage } from '@/vuejs/services/utils'
import { Product } from '@/vuejs/types/Product'
import { BannerSearch } from '@/vuejs/types/BannerSearch'

const PER_PAGE = 36

export function useProductsList() {
  const route = useRoute()
  const productStore = useProductStore()
  const categoryStore = useCategoryStore()
  const sellerStore = useSellerStore()
  const bannerSearchStore = useBannerSearchStore()
  // TODO (MKP-1411): Favoris temporairement désactivés - à rétablir via DJUST
  // const favoriteStore = useFavoriteStore()
  const { adherentTarifShowcases } = storeToRefs(useUserStore())

  const {
    products,
    selectedCategoryId,
    selectedSearchCategory,
    selectedSearchSeller,
    selectedProperties,
    selectedSellerId,
    searchTerms,
  } = storeToRefs(productStore)

  const isLoading = ref<boolean>(false)
  const resultNotFound = ref<boolean>(false)
  const loadMoreLoading = ref<boolean>(false)
  const allowBannerSearch = ref<boolean>(false)
  const internalProducts = ref<Product[]>([])
  const internalAccordCadres = ref<Product[]>([])
  const accordCadresCount = ref<number>(0)
  const currentCount = ref<number>(0)
  const paramsProducts = ref<Record<string, unknown>>(null)
  const searchId = ref<number>(0)

  const count = computed(() => products.value?.resultsCount)

  const termsOrCategoryFilterName = computed((): string => {
    if (searchTerms.value) return searchTerms.value
    if (selectedSearchCategory.value) return selectedSearchCategory.value.name
    if (selectedSearchSeller.value) return selectedSearchSeller.value.name
    return ''
  })

  const isKeywordSearchMode = computed(() => Boolean(searchTerms.value))

  const resultsHeadlinePrefix = computed((): string => {
    if (isKeywordSearchMode.value) return 'Votre recherche :'
    if (selectedSearchCategory.value) return 'Catégorie :'
    if (selectedSearchSeller.value) return 'Partenaire :'
    return ''
  })

  const emptyResultsMessage = computed((): string => {
    const label = termsOrCategoryFilterName.value
    if (!label) {
      return "Aucun résultat n'a été trouvé."
    }
    if (isKeywordSearchMode.value) {
      return `Aucun résultat n'a été trouvé pour la recherche « ${label} »`
    }
    if (selectedSearchCategory.value) {
      return `Aucun produit disponible dans la catégorie « ${label} »`
    }
    if (selectedSearchSeller.value) {
      return `Aucun résultat pour le partenaire « ${label} »`
    }
    return `Aucun résultat pour « ${label} »`
  })

  const resultsHeadlineLine = computed((): string => {
    const prefix = resultsHeadlinePrefix.value
    const name = termsOrCategoryFilterName.value
    return prefix ? `${prefix} ${name}` : name
  })

  const findBannerSearchForCategory = computed<BannerSearch>(() =>
    bannerSearchStore.bannersSearch.find(
      (x) => x.category === paramsProducts.value?.categories,
    ),
  )

  const bannerSearchImg = computed<string | null>(() => {
    if (!findBannerSearchForCategory.value) return null
    return window.innerWidth < 768
      ? getImage(findBannerSearchForCategory.value.mobileImg)
      : getImage(findBannerSearchForCategory.value.desktopImg)
  })

  const bannerSearchLink = computed<string | null>(
    () => findBannerSearchForCategory.value?.link ?? null,
  )

  const loadProducts = async (params: Record<string, unknown>) => {
    await productStore.fetchProductsByParams(params)

    if (params.page === 0) {
      const realFats = products.value.accordCadres ?? []
      internalAccordCadres.value = [...realFats]
      accordCadresCount.value = products.value.accordCadresCount ?? realFats.length
    }

    internalProducts.value.push(
      ...products.value.results.filter(
        (product) =>
          !adherentTarifShowcases.value.some(
            (showcase) => showcase.accordId === product.accordId,
          ),
      ),
    )

    if (route.query.q) {
      const eventLabel =
        products.value?.resultsCount > 0 ? 'view_search_results' : 'no_search_results'
      sendGtmEvent(eventLabel, { search_term: route.query.q })
    }
  }

  const loadMore = async () => {
    loadMoreLoading.value = true
    paramsProducts.value.page = (paramsProducts.value.page as number) + 1
    await loadProducts(paramsProducts.value)
    currentCount.value = internalProducts.value.length
    loadMoreLoading.value = false
  }

  watch(
    () => products.value,
    () => {
      if (products.value.resultsCount !== 0) {
        sendGtmEvent('view_item_list', {
          ecommerce: {
            item_list_id: selectedCategoryId.value ?? selectedSellerId.value,
            items: formatProductGtmEvent(products.value.results),
          },
        })
      }
    },
  )

  watch(
    () => [
      searchTerms.value,
      selectedCategoryId.value,
      selectedSellerId.value,
      selectedProperties.value,
    ],
    () => {
      const queryValue = { ...route.query }

      if (searchTerms.value) {
        queryValue.q = searchTerms.value
      } else {
        delete queryValue.q
      }

      if (selectedCategoryId.value) {
        queryValue.category = selectedCategoryId.value
      } else {
        delete queryValue.category
      }

      if (selectedSellerId.value) {
        queryValue.seller = selectedSellerId.value
      } else {
        delete queryValue.seller
      }

      if (selectedProperties.value) {
        const props = selectedProperties.value as Record<string, string>
        queryValue.property_id = props.property_id
        queryValue.value = props.value
      } else {
        delete queryValue.property_id
        delete queryValue.value
      }

      router.replace({ name: ProductPageList.PRODUCTS, query: queryValue })
    },
  )

  watch(
    () => route.query,
    async (routeObject) => {
      isLoading.value = true
      resultNotFound.value = false
      internalProducts.value = []
      internalAccordCadres.value = []
      accordCadresCount.value = 0
      searchId.value++

      paramsProducts.value = { page: 0, perPage: PER_PAGE, splitSearch: 1 }
      productStore.clearFilters()
      currentCount.value = 0

      if (routeObject.q) {
        productStore.setSearchTerms(routeObject.q)
        paramsProducts.value.name = routeObject.q
      } else {
        productStore.setSearchTerms(null)
      }

      if (routeObject.category) {
        productStore.setSelectedCategory(routeObject.category)
        paramsProducts.value.categories = routeObject.category
        allowBannerSearch.value = !routeObject.seller
        if (!routeObject.seller && bannerSearchStore.bannersSearch.length === 0) {
          bannerSearchStore.init()
        }
      }

      if (routeObject.seller) {
        productStore.setSelectedSeller(routeObject.seller)
        paramsProducts.value.sellers = routeObject.seller
      }

      if (routeObject.property_id && routeObject.value) {
        const properties = {
          property_id: routeObject.property_id,
          value: routeObject.value,
        }
        productStore.setSelectedProperty(properties)
        paramsProducts.value.properties = properties
      }

      try {
        const pending: Promise<void>[] = []
        if (!categoryStore.isLoaded) pending.push(categoryStore.getAllCategories())
        if (sellerStore.allSellers.length === 0) pending.push(sellerStore.getAllSellers())
        if (pending.length > 0) await Promise.all(pending)

        await loadProducts(paramsProducts.value)
        currentCount.value = internalProducts.value.length
      } catch (_error) {
        resultNotFound.value = true
      } finally {
        isLoading.value = false
      }
    },
    { immediate: true, deep: true },
  )

  // TODO (MKP-1411): Appel temporairement désactivé - à rétablir quand les Favoris seront disponibles via DJUST
  // onBeforeMount(() => favoriteStore.fetchFavorites())

  onUnmounted(() => {
    productStore.searchTerms = null
  })

  return {
    isLoading,
    resultNotFound,
    loadMoreLoading,
    allowBannerSearch,
    internalProducts,
    internalAccordCadres,
    accordCadresCount,
    currentCount,
    count,
    termsOrCategoryFilterName,
    resultsHeadlinePrefix,
    resultsHeadlineLine,
    emptyResultsMessage,
    bannerSearchImg,
    bannerSearchLink,
    loadMore,
    searchId,
  }
}
