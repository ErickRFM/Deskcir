module.exports = {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./public/**/*.html"
  ],
  theme: {
    extend: {
      colors: {
        brand: '#00798E',
        brandHover: '#006070',
        darkbg: '#0a0f1c',
        darkcard: '#111827',
        darkborder: '#1f2937'
      },
      borderRadius: {
        xl2: '14px',
        xl3: '20px'
      },
      boxShadow: {
        soft: '0 6px 14px rgba(0,0,0,.06)',
        softDark: '0 6px 14px rgba(0,0,0,.25)',
        float: '0 15px 35px rgba(0,0,0,.08)'
      }
    }
  },
  corePlugins:{ preflight:false },
  plugins: []
}