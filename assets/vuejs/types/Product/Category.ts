export interface Category {
  id: number
  name: string
  parentId: number
  image: string
  productCount: number
  checked: boolean
  children: Array<Category>
}
