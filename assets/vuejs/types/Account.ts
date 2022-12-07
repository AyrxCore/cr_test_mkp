interface BuyerDefaultAddress {
    id: number
    street: string
    postcode: string
    city: string
    province: null | string
}

interface AccountBuyer {
    id: number
    name: string
    avatar: string
    phone: string
    default_address: BuyerDefaultAddress
    number: number
    email: string
    website: string
}

export interface Account {
    id: string
    lastConnexion: Date
    buyer: AccountBuyer
}
