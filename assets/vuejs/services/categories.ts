import { Category } from '@/vuejs/types/Product/Category'

export function sortAscName(categories: Category[]): Category[] {
  return [...categories]
    .sort((catA: Category, catB: Category) =>
      catA.name.localeCompare(catB.name),
    )
    .map((category) => ({
      ...category,
      ...(category.children?.length && {
        children: sortAscName(category.children),
      }),
    }))
}
