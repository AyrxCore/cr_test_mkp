export function gtmCartTrackingEvent(eventLabel, cart, confirmation = null) {
  window.dataLayer.push({ ecommerce: null })
  const itemsObject = []
  const orders = []
  let shippingValue = 0
  Object.entries(cart.orders).forEach(([key, value], index) => {
    orders.push(value.id)
    Object.entries(value.items).forEach(([childKey, childValue], childIIndex) => {
      const price = (childValue.total_excluding_taxes / 100)
      shippingValue += price
      itemsObject.push({
        item_id: childValue.variant.product.id,
        item_name: childValue.variant.product.name.default,
        item_variant: childValue.variant.id,
        price: price,
        quantity: childValue.quantity,
      })
    })
  })

  const gtmObject = {
    event: eventLabel,
    order_id: orders,
    order_nb: orders.length,
    ecommerce: {
      currency: 'EUR',
      value: (cart.total_excluding_taxes / 100),
      items: itemsObject,
    },
  }

  if (confirmation) {
    gtmObject.payment_type = cart.payment_method?.name.default
    gtmObject.ecommerce.transaction_id = confirmation.payment_id ?? cart.id
    gtmObject.ecommerce.shipping = (cart.total_excluding_taxes / 100) - shippingValue
  }

  window.dataLayer?.push(gtmObject)
}
