import BaseClientService from '@/vuejs/services/BaseClientService'
import { Contact } from '@/vuejs/types/Contact'

export default class ContactHttpClient extends BaseClientService {
  public getMotifs<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('contact/list-motifs')
      .then((response) => response.data)
  }

  public sendEmail<T extends []>(contact: Contact): Promise<T> {
    return this.apiClient
      .postForm<T>('contact/send-email', contact)
      .then((response) => response.data)
  }
}
