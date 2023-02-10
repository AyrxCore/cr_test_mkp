export interface AccountAccordCadre {
  accordCadreId: number
  accountId: number
  id: string
  status: string
  createdAt: Date
  updatedAt: Date
}

export interface AccordCadre {
  id: number
  reference: string
  name: string
  description?: string
  properties: Array<any>

  categories: Array<any>
  accountAccordCadre: AccountAccordCadre
}
