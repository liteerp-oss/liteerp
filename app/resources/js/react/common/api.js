import axios from "axios";

const api = axios.create({
  baseURL: "/api", 
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    "App-Language": localStorage.getItem("lang") || import.meta.env.VITE_APP_LOCALE || "en",
  },
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    const businessToken = localStorage.getItem("business-access");
    const lang = localStorage.getItem("lang") || "en";
    if (token) config.headers.Authorization = `Bearer ${token}`;
    if(businessToken) config.headers['business-access'] = businessToken;
    if(lang) config.headers['App-Language'] = lang;
    return config;
  },
  (error) => Promise.reject(error)
);

api.interceptors.response.use(
  (response) => response.data,
  (error) => {
    const status = error.response?.status;
    if (status === 401) {
      localStorage.removeItem("token");
      window.location.href = "/dashboard/login";
    }

    console.error("API Error:", error.response?.data || error.message);
    return Promise.reject(error);
  }
);

export default api;
