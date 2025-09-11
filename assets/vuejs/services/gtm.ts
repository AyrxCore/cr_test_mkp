import { Product } from '@/vuejs/types/Product'
import { Cart } from '@/vuejs/types/Cart.ts'

export const sendGtmEvent = (eventName: string, additionalData = null) => {
  const eventData = { event: eventName, ...additionalData }
  window.dataLayer?.push(eventData)
}

export const formatProductGtmEvent = (products: Product[]) => {
  return products
    .filter((product) => !product.isAccordCadre)
    .slice(0, 10)
    .map((product) => ({
      item_id: product.id,
      item_name: product.name,
      item_partner_name: product.seller?.name,
      item_partner_id: product.seller?.id,
      discount: product.priceReference - product.price,
      price: product.price,
    }))
}

export const formatCartItemsGtmEvent = (cart: Cart) => {
  const itemsObject = []
  let shippingValue = 0
  Object.entries(cart.orders).forEach(([key, value], index) => {
    Object.entries(value.items).forEach(
      ([childKey, childValue], childIIndex) => {
        const price = childValue.total_excluding_taxes / 100
        shippingValue += price
        itemsObject.push({
          item_id: childValue.variant.product.id,
          item_name: childValue.variant.product.name.default,
          item_variant: childValue.variant.id,
          price: price,
          quantity: childValue.quantity,
        })
      },
    )
  })
  return itemsObject
}
