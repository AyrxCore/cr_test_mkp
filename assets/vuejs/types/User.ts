// Décrit l'objet user
export interface User {
  email: string
  roles: string[]
  upplerDatas: {
    username: string
    email: string
    company: string
    firstname: string
    lastname: string
  }
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
