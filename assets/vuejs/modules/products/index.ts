import { ref } from 'vue'

export const status = ref({
  not_activated: 'NOT_ACTIVATED',
  pending: 'PENDING',
  activated: 'ACTIVATED',
})

export const filterType = {
  category: 'CATEGORY',
  property: 'PROPERTY',
  company: 'COMPANY',
  name: 'NAME',
}

export async function addProductToCartGoogleAnalytics(
  product,
  variantId,
  quantity,
  price = null,
) {
  const priceValue = price ?? product.price

  window.dataLayer.push({ ecommerce: null })
  const itemObject = {
    item_id: product.id,
    item_name: product.name,
    affiliation: product.seller.name, // Nom du partenaire
    item_variant: variantId,
    price: priceValue,
    quantity: quantity,
    item_category: null,
  }
  const categories = Object.entries(product.categories)
  if (categories.length > 0) {
    itemObject.item_category = categories[0][1]
  }

  await window.dataLayer?.push({
    event: 'add_to_cart',
    ecommerce: {
      currency: 'EUR',
      value: priceValue,
      items: [itemObject],
    },
  })
}
