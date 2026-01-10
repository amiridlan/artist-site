/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        // Primary green accent (10% - for buttons, CTAs, highlights)
        primary: {
          50: '#e8f5e9',
          100: '#c8e6c9',
          200: '#a5d6a7',
          300: '#81c784',
          400: '#66bb6a',
          500: '#288800',  // Main accent color
          600: '#237700',
          700: '#1e6600',
          800: '#195500',
          900: '#144400',
        },
        // Background colors (60% + 30%)
        surface: {
          50: '#ffffff',
          100: '#F9F9F7',   // 60% - Main background (primary)
          200: '#F1F1ED',   // 30% - Cards, sections (secondary)
          300: '#e8e8e4',
        },
        // Neutral grays for text
        neutral: {
          50: '#fafafa',
          100: '#f5f5f5',
          200: '#e5e5e5',
          300: '#d4d4d4',
          400: '#a3a3a3',
          500: '#737373',
          600: '#525252',
          700: '#404040',
          800: '#262626',
          900: '#1A1A1A',
        },
      },
      fontFamily: {
        'avant-garde': ['ITC Avant Garde Gothic Std', 'Avant Garde', 'Century Gothic', 'sans-serif'],
        outfit: ['Outfit', 'sans-serif'],
        'noto-jp': ['Noto Sans JP', 'sans-serif'],
      },
      backgroundImage: {},
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
        'neon': '0 0 20px rgba(40, 136, 0, 0.5)',
        'glow': '0 4px 20px rgba(40, 136, 0, 0.15)',
      },
    },
  },
  plugins: [],
}
