/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./**/*.php",
    "./js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        premium: {
          50: '#fcfaf6',
          100: '#f6f1e6',
          200: '#ebdcc5',
          300: '#dec19d',
          400: '#d2a070',
          500: '#c5a059', // Our custom Gold/Champagne primary color
          600: '#b87c3f',
          700: '#996035',
          800: '#7e5031',
          900: '#65432a',
        },
        dark: '#111111',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
