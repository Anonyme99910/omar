<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg">
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-20 border-b border-gray-200 bg-primary-500">
          <h1 class="text-2xl font-bold text-white">🦉 LinguaLearn</h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
          <router-link
            v-for="item in navigation"
            :key="item.name"
            :to="item.href"
            class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors"
            :class="{ 'bg-primary-100 text-primary-600 font-semibold': isActive(item.href) }"
          >
            <component :is="item.icon" class="w-5 h-5 mr-3" />
            {{ item.name }}
          </router-link>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t border-gray-200">
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
              A
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-gray-700">Admin</p>
              <p class="text-xs text-gray-500">admin@example.com</p>
            </div>
            <button
              @click="logout"
              class="text-gray-400 hover:text-red-600 transition-colors"
              title="Logout"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router';
import api from '../services/api';

const router = useRouter();
const route = useRoute();

const navigation = [
  {
    name: 'Dashboard',
    href: '/dashboard',
    icon: 'svg',
  },
  {
    name: 'Courses',
    href: '/courses',
    icon: 'svg',
  },
  {
    name: 'Lessons',
    href: '/lessons',
    icon: 'svg',
  },
  {
    name: 'Exercises',
    href: '/exercises',
    icon: 'svg',
  },
  {
    name: 'Users',
    href: '/users',
    icon: 'svg',
  },
  {
    name: 'Analytics',
    href: '/analytics',
    icon: 'svg',
  },
];

const isActive = (href) => {
  return route.path === href || route.path.startsWith(href + '/');
};

const logout = async () => {
  try {
    await api.logout();
  } catch (error) {
    console.error('Logout error:', error);
  } finally {
    localStorage.removeItem('admin_token');
    router.push('/login');
  }
};
</script>
