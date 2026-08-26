export interface CustomerAccount {
  id: string
  email: string
  firstName: string
  lastName: string
  name: string
  customerTags?: Array<{ id: string; label?: string }>
  [key: string]: unknown
}

export interface ExternalApiDataBuyer {
  name: string
  [key: string]: unknown
}

export interface ExternalApiDataEntity {
  externalApiData: {
    customerAccount?: CustomerAccount
    buyer?: ExternalApiDataBuyer
    [key: string]: unknown
  }
}
