/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./App.{js,jsx,ts,tsx}", "./src/**/*.{js,jsx,ts,tsx}"],
  theme: {
    extend: {
      colors: {
        primary: "#3498db",
        luxury: {
          dark: "#0d0d13",
          card: "#161622",
          border: "#232335",
          accent: "#1e1e2f",
        }
      }
    },
  },
  plugins: [],
}
