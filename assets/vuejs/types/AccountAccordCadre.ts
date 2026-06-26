export enum AccountAccordCadreStatus {
  NOT_ACTIVATED = 'NOT_ACTIVATED',
  PENDING = 'PENDING',
  ACTIVATED = 'ACTIVATED',
}

export interface AccountAccordCadre {
  id: string
  accordId: string
  status: AccountAccordCadreStatus
  createdAt: Date
  updatedAt: Date
}
