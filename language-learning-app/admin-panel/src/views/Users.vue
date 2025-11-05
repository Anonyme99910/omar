<template>
  <Sidebar>
    <div class="p-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Users</h1>
          <p class="text-gray-600 mt-1">Manage platform users</p>
        </div>
        <div class="flex gap-3">
          <select v-model="filterRole" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
          <select v-model="filterGuest" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <option value="">All Users</option>
            <option value="false">Regular</option>
            <option value="true">Guest</option>
          </select>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total Users</p>
              <p class="text-3xl font-bold text-primary-500">{{ stats.total }}</p>
            </div>
            <div class="text-4xl">👥</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Active Users</p>
              <p class="text-3xl font-bold text-green-500">{{ stats.active }}</p>
            </div>
            <div class="text-4xl">✅</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Guest Users</p>
              <p class="text-3xl font-bold text-orange-500">{{ stats.guests }}</p>
            </div>
            <div class="text-4xl">🎭</div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Admins</p>
              <p class="text-3xl font-bold text-purple-500">{{ stats.admins }}</p>
            </div>
            <div class="text-4xl">👑</div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
      </div>

      <!-- Users Table -->
      <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">XP</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Streak</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-primary-600 font-bold">{{ user.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'" 
                      class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ user.role === 'admin' ? '👑 Admin' : '👤 User' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span class="font-bold text-primary-600">{{ user.total_xp }}</span> XP
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span class="flex items-center">
                  <span class="mr-1">🔥</span>
                  {{ user.current_streak }} days
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="user.is_guest ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'" 
                      class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ user.is_guest ? '🎭 Guest' : '✅ Regular' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="viewUser(user)" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredUsers.length === 0" class="text-center py-12">
          <p class="text-gray-500">No users found</p>
        </div>
      </div>

      <!-- User Detail Modal -->
      <div v-if="showModal && selectedUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
        <div class="bg-white rounded-xl p-8 max-w-2xl w-full mx-4">
          <h2 class="text-2xl font-bold mb-6">User Details</h2>

          <div class="space-y-4">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-20 w-20 rounded-full bg-primary-100 flex items-center justify-center">
                <span class="text-primary-600 font-bold text-3xl">{{ selectedUser.name.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="ml-6">
                <h3 class="text-xl font-bold">{{ selectedUser.name }}</h3>
                <p class="text-gray-600">{{ selectedUser.email }}</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4">
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total XP</p>
                <p class="text-2xl font-bold text-primary-600">{{ selectedUser.total_xp }}</p>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Current Streak</p>
                <p class="text-2xl font-bold text-orange-600">🔥 {{ selectedUser.current_streak }} days</p>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Longest Streak</p>
                <p class="text-2xl font-bold text-purple-600">{{ selectedUser.longest_streak }} days</p>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Account Type</p>
                <p class="text-2xl font-bold">{{ selectedUser.is_guest ? '🎭 Guest' : '✅ Regular' }}</p>
              </div>
            </div>

            <div class="pt-4">
              <p class="text-sm text-gray-600">Role</p>
              <p class="text-lg font-semibold">{{ selectedUser.role === 'admin' ? '👑 Administrator' : '👤 User' }}</p>
            </div>

            <div>
              <p class="text-sm text-gray-600">Member Since</p>
              <p class="text-lg font-semibold">{{ formatDate(selectedUser.created_at) }}</p>
            </div>
          </div>

          <div class="flex gap-3 pt-6">
            <button @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition-colors">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Sidebar from '../components/Sidebar.vue';

const users = ref([]);
const filterRole = ref('');
const filterGuest = ref('');
const loading = ref(true);
const showModal = ref(false);
const selectedUser = ref(null);

const stats = computed(() => ({
  total: users.value.length,
  active: users.value.filter(u => !u.is_guest).length,
  guests: users.value.filter(u => u.is_guest).length,
  admins: users.value.filter(u => u.role === 'admin').length
}));

const filteredUsers = computed(() => {
  let filtered = users.value;
  
  if (filterRole.value) {
    filtered = filtered.filter(u => u.role === filterRole.value);
  }
  
  if (filterGuest.value) {
    const isGuest = filterGuest.value === 'true';
    filtered = filtered.filter(u => u.is_guest === isGuest);
  }
  
  return filtered;
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const fetchUsers = async () => {
  loading.value = true;
  // Mock data
  users.value = [
    { id: 1, name: 'Admin User', email: 'admin@duolingo.com', role: 'admin', total_xp: 1500, current_streak: 15, longest_streak: 30, is_guest: false, created_at: '2024-01-01' },
    { id: 2, name: 'John Doe', email: 'john@example.com', role: 'user', total_xp: 450, current_streak: 7, longest_streak: 12, is_guest: false, created_at: '2024-02-15' },
    { id: 3, name: 'Jane Smith', email: 'jane@example.com', role: 'user', total_xp: 280, current_streak: 3, longest_streak: 8, is_guest: false, created_at: '2024-03-20' },
    { id: 4, name: 'Guest_a1b2c3', email: 'guest_123@temp.com', role: 'user', total_xp: 50, current_streak: 1, longest_streak: 1, is_guest: true, created_at: '2024-11-05' }
  ];
  loading.value = false;
};

const viewUser = (user) => {
  selectedUser.value = user;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedUser.value = null;
};

const deleteUser = (user) => {
  if (confirm(`Delete user "${user.name}"?`)) {
    users.value = users.value.filter(u => u.id !== user.id);
  }
};

onMounted(() => {
  fetchUsers();
});
</script>
