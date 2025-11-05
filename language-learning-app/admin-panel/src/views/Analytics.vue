<template>
  <Sidebar>
    <div class="p-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Analytics</h1>
        <p class="text-gray-600 mt-1">Platform statistics and insights</p>
      </div>

      <!-- Overview Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white text-opacity-80 text-sm">Total Users</p>
              <p class="text-4xl font-bold mt-2">{{ stats.totalUsers }}</p>
              <p class="text-sm mt-2 text-white text-opacity-80">+12% this month</p>
            </div>
            <div class="text-5xl opacity-20">👥</div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white text-opacity-80 text-sm">Active Learners</p>
              <p class="text-4xl font-bold mt-2">{{ stats.activeUsers }}</p>
              <p class="text-sm mt-2 text-white text-opacity-80">Last 7 days</p>
            </div>
            <div class="text-5xl opacity-20">📚</div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white text-opacity-80 text-sm">Lessons Completed</p>
              <p class="text-4xl font-bold mt-2">{{ stats.lessonsCompleted }}</p>
              <p class="text-sm mt-2 text-white text-opacity-80">This week</p>
            </div>
            <div class="text-5xl opacity-20">✅</div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white text-opacity-80 text-sm">Total XP Earned</p>
              <p class="text-4xl font-bold mt-2">{{ stats.totalXP.toLocaleString() }}</p>
              <p class="text-sm mt-2 text-white text-opacity-80">All time</p>
            </div>
            <div class="text-5xl opacity-20">⚡</div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Popular Courses -->
        <div class="bg-white rounded-xl shadow-md p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Popular Courses</h2>
          <div class="space-y-4">
            <div v-for="course in popularCourses" :key="course.id" class="flex items-center">
              <span class="text-2xl mr-3">{{ course.icon }}</span>
              <div class="flex-1">
                <div class="flex justify-between items-center mb-1">
                  <span class="font-medium">{{ course.name }}</span>
                  <span class="text-sm text-gray-600">{{ course.students }} students</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-primary-500 h-2 rounded-full transition-all" 
                    :style="{ width: course.percentage + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- User Growth -->
        <div class="bg-white rounded-xl shadow-md p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">User Growth</h2>
          <div class="space-y-3">
            <div v-for="month in userGrowth" :key="month.month" class="flex items-center justify-between">
              <span class="text-gray-700">{{ month.month }}</span>
              <div class="flex items-center">
                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                  <div 
                    class="bg-blue-500 h-2 rounded-full" 
                    :style="{ width: (month.users / 50 * 100) + '%' }"
                  ></div>
                </div>
                <span class="font-bold text-blue-600 w-12 text-right">{{ month.users }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Engagement Metrics -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Average Session Time</h3>
          <div class="text-center">
            <p class="text-5xl font-bold text-primary-600 mb-2">24</p>
            <p class="text-gray-600">minutes</p>
            <div class="mt-4 flex items-center justify-center text-green-600">
              <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-semibold">+8% vs last week</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Completion Rate</h3>
          <div class="text-center">
            <p class="text-5xl font-bold text-blue-600 mb-2">78%</p>
            <p class="text-gray-600">of started lessons</p>
            <div class="mt-4 flex items-center justify-center text-green-600">
              <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-semibold">+5% vs last week</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Daily Active Users</h3>
          <div class="text-center">
            <p class="text-5xl font-bold text-purple-600 mb-2">156</p>
            <p class="text-gray-600">users today</p>
            <div class="mt-4 flex items-center justify-center text-green-600">
              <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-semibold">+15% vs yesterday</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Activity</h2>
        <div class="space-y-4">
          <div v-for="activity in recentActivity" :key="activity.id" class="flex items-center p-4 bg-gray-50 rounded-lg">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
              <span class="text-xl">{{ activity.icon }}</span>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-sm font-medium text-gray-900">{{ activity.user }}</p>
              <p class="text-sm text-gray-600">{{ activity.action }}</p>
            </div>
            <div class="text-sm text-gray-500">{{ activity.time }}</div>
          </div>
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '../components/Sidebar.vue';

const stats = ref({
  totalUsers: 1247,
  activeUsers: 892,
  lessonsCompleted: 3456,
  totalXP: 125430
});

const popularCourses = ref([
  { id: 1, name: 'English for Beginners', icon: '🇬🇧', students: 450, percentage: 90 },
  { id: 2, name: 'Spanish Basics', icon: '🇪🇸', students: 320, percentage: 64 },
  { id: 3, name: 'French Fundamentals', icon: '🇫🇷', students: 280, percentage: 56 },
  { id: 4, name: 'German Essentials', icon: '🇩🇪', students: 197, percentage: 39 }
]);

const userGrowth = ref([
  { month: 'January', users: 45 },
  { month: 'February', users: 38 },
  { month: 'March', users: 42 },
  { month: 'April', users: 35 },
  { month: 'May', users: 48 },
  { month: 'June', users: 50 }
]);

const recentActivity = ref([
  { id: 1, user: 'John Doe', action: 'Completed "Greetings & Introductions"', icon: '✅', time: '2 min ago' },
  { id: 2, user: 'Jane Smith', action: 'Started "Spanish Basics" course', icon: '🇪🇸', time: '5 min ago' },
  { id: 3, user: 'Mike Johnson', action: 'Earned 50 XP', icon: '⚡', time: '10 min ago' },
  { id: 4, user: 'Sarah Williams', action: 'Achieved 7-day streak', icon: '🔥', time: '15 min ago' },
  { id: 5, user: 'Guest_abc123', action: 'Created account', icon: '👤', time: '20 min ago' }
]);

onMounted(() => {
  // Data is already set in refs
});
</script>
