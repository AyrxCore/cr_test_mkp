import { Product } from '@/vuejs/types/Product'
import { Cart } from '@/vuejs/types/Cart.ts'
import { useProductStore } from '@/vuejs/stores/product'

export const sendGtmEvent = (eventName: string, additionalData = null) => {
  const eventData = { event: eventName, ...additionalData }
  window.dataLayer?.push(eventData)
}

export const formatProductGtmEvent = (products: Product[]) => {
  const productStore = useProductStore()
  return products
    .filter((product) => !productStore.isAccordCadre(product))
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
  if (!cart?.cartOrders) return []

  const itemsObject = []
  let shippingValue = 0
  Object.entries(cart.cartOrders).forEach(([key, value], index) => {
    Object.entries(value.products).forEach(
      ([childKey, childValue], childIIndex) => {
        const price = childValue.unitPrice * childValue.quantity
        shippingValue += price
        itemsObject.push({
          item_id: childValue.offerPriceId,
          item_name: childValue.name,
          item_variant: childValue.variantId,
          price: price,
          quantity: childValue.quantity,
        })
      },
    )
  })
  return itemsObject
}
