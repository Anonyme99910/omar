<template>
  <Sidebar>
    <div class="p-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Lessons</h1>
          <p class="text-gray-600 mt-1">Manage lessons for each course</p>
        </div>
        <button
          @click="openCreateModal"
          class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center shadow-lg transition-all"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Lesson
        </button>
      </div>

      <!-- Course Filter -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Course</label>
        <select
          v-model="selectedCourseId"
          @change="filterLessons"
          class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
        >
          <option value="">All Courses</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.flag_icon }} {{ course.name }}
          </option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
      </div>

      <!-- Lessons Table -->
      <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lesson</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">XP Reward</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="lesson in filteredLessons" :key="lesson.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="text-2xl mr-3">{{ lesson.icon || '📚' }}</span>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ lesson.title }}</div>
                    <div class="text-sm text-gray-500">{{ lesson.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-900">{{ getCourseName(lesson.course_id) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getDifficultyClass(lesson.difficulty)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ lesson.difficulty }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                +{{ lesson.xp_reward }} XP
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ lesson.order }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="lesson.is_locked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'" 
                      class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ lesson.is_locked ? '🔒 Locked' : '🔓 Unlocked' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="editLesson(lesson)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                <button @click="deleteLesson(lesson)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredLessons.length === 0" class="text-center py-12">
          <p class="text-gray-500">No lessons found</p>
        </div>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
        <div class="bg-white rounded-xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <h2 class="text-2xl font-bold mb-6">{{ editingLesson ? 'Edit Lesson' : 'Create New Lesson' }}</h2>

          <form @submit.prevent="saveLesson" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
              <select v-model="form.course_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Select a course</option>
                <option v-for="course in courses" :key="course.id" :value="course.id">
                  {{ course.flag_icon }} {{ course.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
              <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="e.g., Greetings & Introductions" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Lesson description..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                <select v-model="form.difficulty" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon (Emoji)</label>
                <input v-model="form.icon" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="📚" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XP Reward</label>
                <input v-model.number="form.xp_reward" type="number" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="10" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                <input v-model.number="form.order" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="0" />
              </div>
            </div>

            <div class="flex items-center">
              <input v-model="form.is_locked" type="checkbox" id="is_locked" class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" />
              <label for="is_locked" class="ml-2 block text-sm text-gray-900">Lock this lesson</label>
            </div>

            <div class="flex gap-3 pt-4">
              <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition-colors">
                Cancel
              </button>
              <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-lg font-semibold transition-colors">
                {{ editingLesson ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Sidebar from '../components/Sidebar.vue';

const courses = ref([]);
const lessons = ref([]);
const selectedCourseId = ref('');
const loading = ref(true);
const showModal = ref(false);
const editingLesson = ref(null);

const form = ref({
  course_id: '',
  title: '',
  description: '',
  difficulty: 'beginner',
  xp_reward: 10,
  order: 0,
  icon: '📚',
  is_locked: false
});

const filteredLessons = computed(() => {
  if (!selectedCourseId.value) return lessons.value;
  return lessons.value.filter(l => l.course_id == selectedCourseId.value);
});

const getCourseName = (courseId) => {
  const course = courses.value.find(c => c.id == courseId);
  return course ? `${course.flag_icon} ${course.name}` : 'Unknown';
};

const getDifficultyClass = (difficulty) => {
  const classes = {
    beginner: 'bg-green-100 text-green-800',
    intermediate: 'bg-yellow-100 text-yellow-800',
    advanced: 'bg-red-100 text-red-800'
  };
  return classes[difficulty] || classes.beginner;
};

const fetchData = async () => {
  loading.value = true;
  // Mock data - replace with actual API calls
  courses.value = [
    { id: 1, name: 'English for Beginners', flag_icon: '🇬🇧' },
    { id: 2, name: 'Spanish Basics', flag_icon: '🇪🇸' },
    { id: 3, name: 'French Fundamentals', flag_icon: '🇫🇷' }
  ];

  lessons.value = [
    { id: 1, course_id: 1, title: 'Greetings & Introductions', description: 'Learn basic greetings', difficulty: 'beginner', xp_reward: 10, order: 1, icon: '👋', is_locked: false },
    { id: 2, course_id: 1, title: 'Numbers 1-20', description: 'Count to 20', difficulty: 'beginner', xp_reward: 10, order: 2, icon: '🔢', is_locked: false },
    { id: 3, course_id: 1, title: 'Colors & Shapes', description: 'Basic colors', difficulty: 'beginner', xp_reward: 10, order: 3, icon: '🎨', is_locked: false },
    { id: 4, course_id: 2, title: 'Hola! Basic Greetings', description: 'Spanish greetings', difficulty: 'beginner', xp_reward: 10, order: 1, icon: '👋', is_locked: false },
  ];
  loading.value = false;
};

const openCreateModal = () => {
  editingLesson.value = null;
  form.value = {
    course_id: '',
    title: '',
    description: '',
    difficulty: 'beginner',
    xp_reward: 10,
    order: lessons.value.length,
    icon: '📚',
    is_locked: false
  };
  showModal.value = true;
};

const editLesson = (lesson) => {
  editingLesson.value = lesson;
  form.value = { ...lesson };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingLesson.value = null;
};

const saveLesson = () => {
  // Mock save - replace with actual API call
  if (editingLesson.value) {
    const index = lessons.value.findIndex(l => l.id === editingLesson.value.id);
    lessons.value[index] = { ...form.value, id: editingLesson.value.id };
  } else {
    lessons.value.push({ ...form.value, id: Date.now() });
  }
  closeModal();
};

const deleteLesson = (lesson) => {
  if (confirm(`Delete lesson "${lesson.title}"?`)) {
    lessons.value = lessons.value.filter(l => l.id !== lesson.id);
  }
};

const filterLessons = () => {
  // Filtering is handled by computed property
};

onMounted(() => {
  fetchData();
});
</script>
