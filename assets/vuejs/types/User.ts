import { Account } from '@/vuejs/types/Account'
// Décrit l'objet user
export interface User {
  email: string
  roles: string[]
  account: Account
  username: string
  firstName: string
  lastName: string
}

// Décrit le state général du store user
export interface UserStoreState {
  user: User
}

// Décrit le bloc de données nécessaire à l'obtention du token
export interface AuthenticateUserDatas {
  username: string
  password: string
}

// décrit le bloc retourné par le back avec le token
export interface AuthenticateResponse {
  token: string
}

export enum LoginResponse {
  UserDisabled = 'user_disabled',
  UserEmptyAccount = 'user_empty_account',
  InvalidCredentials = 'Identifiants invalides.',
}
