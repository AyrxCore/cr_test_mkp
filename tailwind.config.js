const plugin = require('tailwindcss/plugin')
const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  mode: 'jit',
  content: {
    enabled: process.env.NODE_ENV === 'production',
    content: [
      './assets/**/*.{vue,ts}',
      './src/**/*.{html,js}',
      './templates/**/*.html.twig',
    ],
  },
  theme: {
    extend: {
      // here's how to extend fonts if needed
      fontFamily: {
        sans: [...defaultTheme.fontFamily.sans],
        cotext: '"Cotext"',
      },
      spacing: {
        7.5: '1.875rem',
        8.5: '2.125rem',
      },
      colors: {
        primary: 'var(--primary-color)',
        secondary: 'var(--secondary-color)',
        tertiary: '#0bb0fa',
        'green-qantis': '#65AC5D',
      },
      screens: {
        90: '90%',
        92: '92%',
        94: '94%',
        98: '98%',
      },
      boxShadow: {
        'inner-lighter': 'inset 0 0 100px 100px rgba(255, 255, 255, 0.15)',
        'inner-darker': 'inset 0 0 100px 100px rgba(0, 0, 0, 0.1)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    plugin(function ({ addVariant, e, postcss }) {
      addVariant('firefox', ({ container, separator }) => {
        const isFirefoxRule = postcss.atRule({
          name: '-moz-document',
          params: 'url-prefix()',
        })
        isFirefoxRule.append(container.nodes)
        container.append(isFirefoxRule)
        isFirefoxRule.walkRules((rule) => {
          rule.selector = `.${e(
            `firefox${separator}${rule.selector.slice(1)}`,
          )}`
        })
      })
    }),
  ],
  safelist: ['bg-gray-100'],
}
