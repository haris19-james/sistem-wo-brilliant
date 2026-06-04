/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.{blade.php,js,vue}',
    './resources/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        // Brilliant Brand Colors
        field: {
          DEFAULT: '#00A32A',
          50: '#F0F9F0',
          100: '#E1F2E1',
          200: '#C3E5C3',
          300: '#A5D8A5',
          600: '#00A32A',
          700: '#008F24',
          800: '#007B1F',
          950: '#004D12',
        },
        // Soft Pastel Palette (Ethereal Floral Theme)
        'creamsicle': {
          50: '#FFFBF7',
          100: '#FFF5ED',
          200: '#FFEDDD',
          300: '#FFE5CC',
        },
        'olive': {
          50: '#F9FAF7',
          100: '#F3F5EF',
          200: '#E8ECDE',
          300: '#DCED28',
          600: '#6B7D3E',
          700: '#5A6B34',
        },
        // Soft Pastel Accent Colors
        'pastel': {
          blue: '#B8E0F0',
          'blue-dark': '#7FCCE8',
          orange: '#FFD9B8',
          'orange-dark': '#FFBB80',
          pink: '#F5D7E8',
          'pink-dark': '#EFB8D9',
          purple: '#E4D4F1',
          'purple-dark': '#D9B3E8',
          green: '#D4F9DC',
          'green-dark': '#B8F0CC',
        },
      },
      backgroundImage: {
        'floral-pattern': 'url("data:image/svg+xml,%3Csvg width=\'400\' height=\'400\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cdefs%3E%3ClinearGradient id=\'grad\' x1=\'0%25\' y1=\'0%25\' x2=\'100%25\' y2=\'100%25\'%3E%3Cstop offset=\'0%25\' style=\'stop-color:%23E5F0E8;stop-opacity:0.15\' /%3E%3Cstop offset=\'100%25\' style=\'stop-color:%23F0E5E8;stop-opacity:0.15\' /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width=\'400\' height=\'400\' fill=\'url(%23grad)\' /%3E%3C!-- Floral Elements --%3E%3Cg opacity=\'0.08\'%3E%3C!-- Large Flower Clusters --%3E%3Ccircle cx=\'100\' cy=\'50\' r=\'30\' fill=\'%2300A32A\' /%3E%3Ccircle cx=\'300\' cy=\'350\' r=\'35\' fill=\'%234A90E2\' /%3E%3Ccircle cx=\'350\' cy=\'100\' r=\'25\' fill=\'%23F5A623\' /%3E%3C!-- Leaf accents --%3E%3Cellipse cx=\'80\' cy=\'40\' rx=\'8\' ry=\'15\' fill=\'%236EBF6B\' transform=\'rotate(-30 80 40)\' /%3E%3Cellipse cx=\'320\' cy=\'340\' rx=\'8\' ry=\'15\' fill=\'%236EBF6B\' transform=\'rotate(45 320 340)\' /%3E%3C/g%3E%3C/svg%3E")',
        'floral-subtle': 'radial-gradient(circle at 20% 80%, rgba(110, 191, 107, 0.08) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(74, 144, 226, 0.08) 0%, transparent 50%)',
      },
      backdropBlur: {
        xs: '2px',
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(0, 163, 42, 0.08)',
        'glass-lg': '0 8px 32px 0 rgba(0, 163, 42, 0.12)',
        'inner-glow': 'inset 0 0 0 1px rgba(255, 255, 255, 0.3)',
      },
      borderColor: {
        'glass': 'rgba(255, 255, 255, 0.2)',
        'glass-dark': 'rgba(0, 163, 42, 0.1)',
      },
      animation: {
        'float': 'float 3s ease-in-out infinite',
        'fade-in': 'fadeIn 0.5s ease-in',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
      },
    },
  },
  plugins: [],
};
