import { Contact } from '@/vuejs/types/Contact'
import DefaultClientService from '@/vuejs/services/DefaultClientService'

export default class ContactHttpClient extends DefaultClientService {

  public getToken<T extends []>(): Promise<T> {
    return this.client
      .get<T>('contact/token')
      .then((response) => response.data)
  }

  public getMotifs<T extends []>(): Promise<T> {
    return this.client
      .get<T>('contact/list-motifs')
      .then((response) => response.data)
  }

  public sendEmail<T extends []>(contact: Contact): Promise<T> {
    return this.client
      .post<T>('contact/send-email', contact)
      .then((response) => response.data)
  }
}
