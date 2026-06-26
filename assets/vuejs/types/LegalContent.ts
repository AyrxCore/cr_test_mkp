export interface LegalContent {
  cgu?: string
  legalTerms?: string
  privacyPolicy?: string
}

export interface LegalContentResponse {
  status: string
  data: LegalContent
}

