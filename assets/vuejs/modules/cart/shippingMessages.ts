import { ShippingCostResult } from '@/vuejs/types/Cart.ts'

export interface ShippingMessage {
  text: string
  success: boolean
}

export function getShippingMessages(result: ShippingCostResult): ShippingMessage[] {
  const { type, remainingForFranco, shippingCost } = result

  switch (type) {
    case 'FREE':
      return [
        { text: 'Vous bénéficiez de la livraison gratuite', success: true },
      ]

    case 'STANDARD':
    case 'CATEGORY':
      if (remainingForFranco === 0) {
        return [
          {
            text: 'Vous bénéficiez de la livraison gratuite',
            success: true,
          },
        ]
      }
      return [
        {
          text: `Il vous reste ${remainingForFranco}€ HT de commande pour bénéficier de la livraison gratuite`,
          success: false,
        },
      ]

    case 'WEIGHT':
      return [{ text: `Frais de port : ${shippingCost}€ HT`, success: true }]

    case 'PERCENTAGE':
    case 'FIXED':
    case 'STEPS':
      return []

    default:
      return []
  }
}
