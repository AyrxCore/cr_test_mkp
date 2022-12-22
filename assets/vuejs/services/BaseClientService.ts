import axios, { AxiosInstance, AxiosResponse } from 'axios'
import { useUserStore } from '@/vuejs/stores/user'

class BaseClientService {
  public apiClient: AxiosInstance
  public static self: InstanceType<any>

  public constructor() {
    const headers = {
      accept: 'application/json',
    }

    headers['Content-Type'] = 'application/json'

    this.apiClient = axios.create({
      baseURL: '/api',
      withCredentials: true,
      headers,
    })

    this.apiClient.interceptors.response.use(
      (response: Promise<AxiosResponse> | AxiosResponse | undefined) => {
        return response
      },
      async (error: AxiosError) => {
        const originalConfig = error.config
        if (error.response?.status === 401 && !originalConfig._retry) {
          if (
            error.config.url !== 'token/refresh' &&
            error.config.url !== 'user/me'
          ) {
            return await this.refreshToken().then(() => {
              console.log('error 401 refresh the token ')
              originalConfig.headers = JSON.parse(
                JSON.stringify(originalConfig.headers),
              )
              return Promise.resolve(this.apiClient(originalConfig))
            })
          } else {
            console.log(error.config.url)
          }
        }
        return Promise.reject(error)
      },
    )
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

  private async refreshToken() {
    await this.apiClient.post('token/refresh')
  }
}

export default BaseClientService
