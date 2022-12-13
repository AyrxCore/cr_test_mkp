import axios, { AxiosInstance } from 'axios'
import { useUserStore } from '@/vuejs/stores/user'

class BaseClientService {
  public apiClient: AxiosInstance
  public static self: InstanceType<any>

  public constructor() {
    const userStore = useUserStore()

    const axiosHeaders = {
      accept: 'application/json',
      Authorization: undefined,
    }

    axiosHeaders['Content-Type'] = 'application/json'

    this.apiClient = axios.create({
      baseURL: '/api',
      headers: axiosHeaders,
      withCredentials: true,
    })
  }

  public static get<T extends typeof BaseClientService>(
    this: T,
  ): InstanceType<T> {
    if (this.self) {
      return this.self
    }

    const instance = new this()
    this.self = instance
    return instance as InstanceType<T>
  }
}

export default BaseClientService
