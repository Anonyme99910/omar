import axios from 'axios';

// Use your computer's IP address instead of localhost for mobile access
const API_URL = 'http://10.50.240.89/parfumes/language-learning-app/backend/public/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add token to requests
api.interceptors.request.use(
  (config) => {
    const token = global.userToken;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Handle responses
api.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response?.status === 401) {
      global.userToken = null;
      // Navigate to login
    }
    return Promise.reject(error);
  }
);

export default {
  // Auth
  register: (data) => api.post('/register', data),
  login: (credentials) => api.post('/login', credentials),
  guestLogin: () => api.post('/guest-login'),
  logout: () => api.post('/logout'),
  getMe: () => api.get('/me'),
  convertGuest: (data) => api.post('/convert-guest', data),

  // Courses
  getCourses: () => api.get('/courses'),
  getCourse: (id) => api.get(`/courses/${id}`),
  getCourseProgress: (id) => api.get(`/courses/${id}/progress`),

  // Lessons
  getLesson: (id) => api.get(`/lessons/${id}`),
  startLesson: (id) => api.post(`/lessons/${id}/start`),
  completeLesson: (id) => api.post(`/lessons/${id}/complete`),

  // Exercises
  getExercise: (id) => api.get(`/exercises/${id}`),
  getLessonExercises: (lessonId) => api.get(`/lessons/${lessonId}/exercises`),
  submitAnswer: (id, answer) => api.post(`/exercises/${id}/submit`, { answer }),
};
