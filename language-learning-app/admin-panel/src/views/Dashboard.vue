<template>
  <Sidebar>
    <div class="p-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total Courses</p>
              <p class="text-3xl font-bold text-primary-500">{{ stats.courses }}</p>
            </div>
            <div class="text-4xl">📚</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total Lessons</p>
              <p class="text-3xl font-bold text-blue-500">{{ stats.lessons }}</p>
            </div>
            <div class="text-4xl">📖</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total Users</p>
              <p class="text-3xl font-bold text-purple-500">{{ stats.users }}</p>
            </div>
            <div class="text-4xl">👥</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total Exercises</p>
              <p class="text-3xl font-bold text-orange-500">{{ stats.exercises }}</p>
            </div>
            <div class="text-4xl">✏️</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <router-link
            to="/courses"
            class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-all"
          >
            <span class="text-3xl mr-3">📚</span>
            <div>
              <p class="font-semibold">Manage Courses</p>
              <p class="text-sm text-gray-600">Add, edit, or delete courses</p>
            </div>
          </router-link>

          <router-link
            to="/lessons"
            class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all"
          >
            <span class="text-3xl mr-3">📖</span>
            <div>
              <p class="font-semibold">Manage Lessons</p>
              <p class="text-sm text-gray-600">Create and organize lessons</p>
            </div>
          </router-link>

          <router-link
            to="/users"
            class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all"
          >
            <span class="text-3xl mr-3">👥</span>
            <div>
              <p class="font-semibold">View Users</p>
              <p class="text-sm text-gray-600">See user statistics</p>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '../components/Sidebar.vue';

const stats = ref({
  courses: 5,
  lessons: 12,
  users: 3,
  exercises: 16
});

onMounted(() => {
  // Stats are hardcoded for now, can be fetched from API later
});
</script>
