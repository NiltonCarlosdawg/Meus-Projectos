/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      keyframes:{
        'bounce-twice': {
          '0%, 100%': { transform: 'translateX(20px)' },
          '100%': { transform: 'translateX(0px)' },
        },
    
      },
      animation:{
        'bounce-twice': 'bounce-twice 1s ease-in-out infinite',
      }
    }
  },
  plugins: [],
}