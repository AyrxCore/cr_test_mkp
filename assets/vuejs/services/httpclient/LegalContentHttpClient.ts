import DefaultClientService from '@/vuejs/services/DefaultClientService'
import type { LegalContentResponse } from '../../types/LegalContent'

export default class LegalContentHttpClient extends DefaultClientService {
  public getLegalContent(): Promise<LegalContentResponse> {
    return this.client
      .get<LegalContentResponse>('legal-content')
      .then((response) => response.data)
  }
}

