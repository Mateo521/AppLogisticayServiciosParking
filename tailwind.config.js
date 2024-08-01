/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.{html,js,php}",         // Archivos en el directorio raíz
    "./template-parts/**/*.{html,js,php}" // Archivos en la carpeta template-parts
  ],
  theme: {
    extend: {}, 
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
