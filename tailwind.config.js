/** @type {import('tailwindcss').Config} */
export default {
    content: [
      "./resources/**/**/*.blade.php",
      "./resources/**/**/*.js",
      "./app/View/Components/**/**/*.php",
      "./app/Livewire/**/**/*.php",

      "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
    ],
    theme: {
      fontSize: {
        sm: '0.8rem',
        md: '1.05rem',
        lg: '1.3rem',
        xl: '1.8rem',
      },
      fontFamily: {
        heading: 'Playfair Display, Inter',
        body: 'Merriweather, Inter',
      },
      fontWeight: {
        normal: '400',
        bold: '700',
      },
    },
    daisyui: {
      themes: [
        {
          'light': {
            'primary': '#f9ecc1',
            'secondary': '#98833E',
            'accent': '#c1f5f9',
            'neutral': '#4c566a',
            'base-100': '#eceff4',
            'base-content': '#2e3440',
          },
          'dark': {
            'primary': '#986c3e',
            'secondary': '#513448',
            'accent': '#49adad',
            'neutral': '#23282e',
            'base-100': '#333',
            'base-content': '#dca54c',
          },
        },
      ],
    },
    plugins: [require("daisyui")]
}
