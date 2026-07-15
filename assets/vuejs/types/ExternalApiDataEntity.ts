export interface CustomerAccount {
  id: string
  email: string
  firstName: string
  lastName: string
  customerTags?: Array<{ id: string; label?: string }>
  [key: string]: any
}

export interface ExternalApiDataEntity {
  externalApiData: {
    customerAccount?: CustomerAccount
    [key: string]: any
  }
}
