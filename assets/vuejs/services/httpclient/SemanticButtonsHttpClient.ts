import BaseClientService from '@/vuejs/services/BaseClientService'
import { SemanticButton } from '@/vuejs/types/SemanticButton'

export default class SemanticButtonsHttpClient extends BaseClientService {
  public getSemanticButtons(): Promise<SemanticButton[]> {
    return this.apiClient.get('semantic_buttons').then((response) => {
      return response.data
    })
  }
}
