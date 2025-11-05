<template>
  <Sidebar>
    <div class="p-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Courses</h1>
          <p class="text-gray-600 mt-1">Manage language courses</p>
        </div>
        <button
          @click="openCreateModal"
          class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center shadow-lg transition-all"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Course
        </button>
      </div>

      <!-- Courses Grid -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="course in courses"
          :key="course.id"
          class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow overflow-hidden"
        >
          <!-- Course Header -->
          <div
            class="h-32 flex items-center justify-center text-white text-6xl"
            :style="{ backgroundColor: course.color_primary }"
          >
            {{ course.flag_icon || '🌍' }}
          </div>

          <!-- Course Content -->
          <div class="p-6">
            <div class="flex justify-between items-start mb-3">
              <h3 class="text-xl font-bold text-gray-900">{{ course.name }}</h3>
              <span
                class="px-3 py-1 text-xs font-semibold rounded-full"
                :class="{
                  'bg-green-100 text-green-800': course.difficulty === 'beginner',
                  'bg-yellow-100 text-yellow-800': course.difficulty === 'intermediate',
                  'bg-red-100 text-red-800': course.difficulty === 'advanced'
                }"
              >
                {{ course.difficulty }}
              </span>
            </div>

            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
              {{ course.description || 'No description' }}
            </p>

            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
              <span>{{ course.lessons_count || 0 }} lessons</span>
              <span>{{ course.total_xp }} XP</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button
                @click="editCourse(course)"
                class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 rounded-lg font-medium transition-colors"
              >
                Edit
              </button>
              <button
                @click="viewLessons(course)"
                class="flex-1 bg-primary-50 hover:bg-primary-100 text-primary-600 py-2 rounded-lg font-medium transition-colors"
              >
                Lessons
              </button>
              <button
                @click="deleteCourse(course)"
                class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg font-medium transition-colors"
              >
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Create/Edit Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="closeModal"
      >
        <div class="bg-white rounded-xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <h2 class="text-2xl font-bold mb-6">
            {{ editingCourse ? 'Edit Course' : 'Create New Course' }}
          </h2>

          <form @submit.prevent="saveCourse" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Course Name</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="e.g., English for Beginners"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Language Code</label>
              <input
                v-model="form.language_code"
                type="text"
                required
                maxlength="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="e.g., en, ar, fr"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Flag Icon (Emoji)</label>
              <input
                v-model="form.flag_icon"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="🇬🇧"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Course description..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
              <select
                v-model="form.difficulty"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                <input
                  v-model="form.color_primary"
                  type="color"
                  class="w-full h-12 rounded-lg cursor-pointer"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                <input
                  v-model="form.color_secondary"
                  type="color"
                  class="w-full h-12 rounded-lg cursor-pointer"
                />
              </div>
            </div>

            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-lg font-semibold transition-colors"
              >
                {{ editingCourse ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from '../components/Sidebar.vue';
import api from '../services/api';

const router = useRouter();
const courses = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editingCourse = ref(null);

const form = ref({
  name: '',
  language_code: '',
  flag_icon: '',
  description: '',
  difficulty: 'beginner',
  color_primary: '#58CC02',
  color_secondary: '#89E219',
  is_active: true,
  order: 0
});

const fetchCourses = async () => {
  try {
    loading.value = true;
    const response = await api.getCourses();
    courses.value = response.courses;
  } catch (error) {
    console.error('Error fetching courses:', error);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  editingCourse.value = null;
  form.value = {
    name: '',
    language_code: '',
    flag_icon: '',
    description: '',
    difficulty: 'beginner',
    color_primary: '#58CC02',
    color_secondary: '#89E219',
    is_active: true,
    order: courses.value.length
  };
  showModal.value = true;
};

const editCourse = (course) => {
  editingCourse.value = course;
  form.value = { ...course };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingCourse.value = null;
};

const saveCourse = async () => {
  try {
    if (editingCourse.value) {
      await api.updateCourse(editingCourse.value.id, form.value);
    } else {
      await api.createCourse(form.value);
    }
    closeModal();
    fetchCourses();
  } catch (error) {
    console.error('Error saving course:', error);
    alert('Error saving course');
  }
};

const deleteCourse = async (course) => {
  if (!confirm(`Delete course "${course.name}"?`)) return;
  
  try {
    await api.deleteCourse(course.id);
    fetchCourses();
  } catch (error) {
    console.error('Error deleting course:', error);
    alert('Error deleting course');
  }
};

const viewLessons = (course) => {
  router.push(`/courses/${course.id}/lessons`);
};

onMounted(() => {
  fetchCourses();
});
</script>
