import axios from 'axios'

// Phase 1-এ real backend URL env variable থেকে আসবে
const API_BASE_URL =
  import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'

export const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Phase 1-এ admin auth token attach করার জন্য interceptor এখানে বসবে
