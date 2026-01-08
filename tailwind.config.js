/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#e6fff8',
          100: '#b3ffe8',
          200: '#80ffd8',
          300: '#4dffc8',
          400: '#1affb8',
          500: '#00ff9f',
          600: '#00cc7f',
          700: '#00995f',
          800: '#00663f',
          900: '#00331f',
        },
        accent: {
          purple: '#b026ff',
          yellow: '#ffed4e',
          pink: '#ff26d9',
          blue: '#26d9ff',
        },
        dark: {
          50: '#f5f5f5',
          100: '#e0e0e0',
          200: '#c2c2c2',
          300: '#a3a3a3',
          400: '#858585',
          500: '#666666',
          600: '#4d4d4d',
          700: '#333333',
          800: '#1a1a1a',
          900: '#0a0a0a',
        },
      },
      fontFamily: {
        outfit: ['Outfit', 'sans-serif'],
        'dm-sans': ['DM Sans', 'sans-serif'],
        'noto-jp': ['Noto Sans JP', 'sans-serif'],
      },
      backgroundImage: {
        'gradient-primary': 'linear-gradient(135deg, #00ff9f 0%, #b026ff 100%)',
        'gradient-accent': 'linear-gradient(135deg, #b026ff 0%, #ff26d9 100%)',
        'gradient-neon': 'linear-gradient(135deg, #00ff9f 0%, #26d9ff 50%, #b026ff 100%)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.5s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'scale-in': 'scaleIn 0.3s ease-out',
        'bounce-slow': 'bounce 3s infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.9)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
      },
      boxShadow: {
        'neon': '0 0 20px rgba(0, 255, 159, 0.5)',
        'neon-purple': '0 0 20px rgba(176, 38, 255, 0.5)',
      },
    },
  },
  plugins: [],
}
