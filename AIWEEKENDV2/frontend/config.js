// config.js

const config = {
  development: {
    apiUrl: "http://localhost:3000/AIWEEKENDV2/backend/get_all.php",
  },
  production: {
    apiUrl: "https://www.alphadocere.cl/API/backend/get_all.php",
  },
};

// Detect if we're in production based on the URL
const isProduction = window.location.hostname !== "localhost";

// Export the appropriate API URL
export const apiUrl = isProduction
  ? config.production.apiUrl
  : config.development.apiUrl;
