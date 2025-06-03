import { ProductCategory } from '@/vuejs/types/Product'

export function sortCategories(
  categories: ProductCategory[],
): ProductCategory[] {
  return [...categories]
    .sort((catA: ProductCategory, catB: ProductCategory) =>
      catA.name.localeCompare(catB.name),
    )
    .map((category) => ({
      ...category,
      ...(category.children?.length && {
        children: sortCategories(category.children),
      }),
    }))
}
