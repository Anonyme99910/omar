import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import App from './App.vue'
import Login from './views/Login.vue'
import Dashboard from './views/Dashboard.vue'
import Courses from './views/Courses.vue'
import Lessons from './views/Lessons.vue'
import Exercises from './views/Exercises.vue'
import Users from './views/Users.vue'
import Analytics from './views/Analytics.vue'
import './style.css'

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    { 
      path: '/', 
      redirect: () => {
        return localStorage.getItem('admin_token') ? '/dashboard' : '/login'
      }
    },
    { 
      path: '/login', 
      component: Login,
      meta: { requiresGuest: true }
    },
    { 
      path: '/dashboard', 
      component: Dashboard,
      meta: { requiresAuth: true }
    },
    { 
      path: '/courses', 
      component: Courses,
      meta: { requiresAuth: true }
    },
    { 
      path: '/lessons', 
      component: Lessons,
      meta: { requiresAuth: true }
    },
    { 
      path: '/exercises', 
      component: Exercises,
      meta: { requiresAuth: true }
    },
    { 
      path: '/users', 
      component: Users,
      meta: { requiresAuth: true }
    },
    { 
      path: '/analytics', 
      component: Analytics,
      meta: { requiresAuth: true }
    },
  ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('admin_token')
  
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.meta.requiresGuest && token) {
    next('/dashboard')
  } else {
    next()
  }
})

const app = createApp(App)
app.use(router)
app.mount('#app')
