<template>
  <Sidebar>
    <div class="p-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Exercises</h1>
          <p class="text-gray-600 mt-1">Manage exercises for lessons</p>
        </div>
        <button
          @click="openCreateModal"
          class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center shadow-lg transition-all"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Exercise
        </button>
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Course</label>
          <select v-model="selectedCourseId" @change="onCourseChange" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <option value="">All Courses</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">
              {{ course.flag_icon }} {{ course.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Lesson</label>
          <select v-model="selectedLessonId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <option value="">All Lessons</option>
            <option v-for="lesson in filteredLessons" :key="lesson.id" :value="lesson.id">
              {{ lesson.icon }} {{ lesson.title }}
            </option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
      </div>

      <!-- Exercises Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="exercise in filteredExercises"
          :key="exercise.id"
          class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow overflow-hidden"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-3">
              <span :class="getTypeClass(exercise.type)" class="px-3 py-1 text-xs font-semibold rounded-full">
                {{ exercise.type.replace('_', ' ').toUpperCase() }}
              </span>
              <span class="text-sm text-gray-500">+{{ exercise.xp_reward }} XP</span>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ exercise.question }}</h3>
            
            <div class="text-sm text-gray-600 mb-3">
              <p><strong>Lesson:</strong> {{ getLessonName(exercise.lesson_id) }}</p>
              <p><strong>Answer:</strong> {{ exercise.correct_answer }}</p>
            </div>

            <div v-if="exercise.options" class="mb-3">
              <p class="text-xs text-gray-500 mb-1">Options:</p>
              <div class="flex flex-wrap gap-1">
                <span v-for="(option, idx) in JSON.parse(exercise.options || '[]')" :key="idx" class="px-2 py-1 bg-gray-100 text-xs rounded">
                  {{ option }}
                </span>
              </div>
            </div>

            <div class="flex gap-2 mt-4">
              <button @click="editExercise(exercise)" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 rounded-lg font-medium transition-colors">
                Edit
              </button>
              <button @click="deleteExercise(exercise)" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg font-medium transition-colors">
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!loading && filteredExercises.length === 0" class="text-center py-12 bg-white rounded-xl">
        <p class="text-gray-500">No exercises found</p>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
        <div class="bg-white rounded-xl p-8 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <h2 class="text-2xl font-bold mb-6">{{ editingExercise ? 'Edit Exercise' : 'Create New Exercise' }}</h2>

          <form @submit.prevent="saveExercise" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Lesson *</label>
              <select v-model="form.lesson_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Select a lesson</option>
                <option v-for="lesson in allLessons" :key="lesson.id" :value="lesson.id">
                  {{ lesson.icon }} {{ lesson.title }} ({{ getCourseName(lesson.course_id) }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Exercise Type *</label>
              <select v-model="form.type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="multiple_choice">Multiple Choice</option>
                <option value="translate">Translate</option>
                <option value="speak">Speak</option>
                <option value="listen">Listen</option>
                <option value="match_pairs">Match Pairs</option>
                <option value="fill_blank">Fill in the Blank</option>
                <option value="word_order">Word Order</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Question *</label>
              <textarea v-model="form.question" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="What is the question?"></textarea>
            </div>

            <div v-if="form.type === 'multiple_choice'">
              <label class="block text-sm font-medium text-gray-700 mb-2">Options (comma-separated) *</label>
              <input v-model="optionsText" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Option 1, Option 2, Option 3, Option 4" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer *</label>
              <input v-model="form.correct_answer" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="The correct answer" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Explanation</label>
              <textarea v-model="form.explanation" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="Explain why this is the correct answer..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">XP Reward</label>
                <input v-model.number="form.xp_reward" type="number" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="5" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                <input v-model.number="form.order" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" placeholder="0" />
              </div>
            </div>

            <div class="flex gap-3 pt-4">
              <button type="button" @click="closeModal" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition-colors">
                Cancel
              </button>
              <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-lg font-semibold transition-colors">
                {{ editingExercise ? 'Update' : 'Create' }}
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
const allLessons = ref([]);
const exercises = ref([]);
const selectedCourseId = ref('');
const selectedLessonId = ref('');
const loading = ref(true);
const showModal = ref(false);
const editingExercise = ref(null);
const optionsText = ref('');

const form = ref({
  lesson_id: '',
  type: 'multiple_choice',
  question: '',
  options: null,
  correct_answer: '',
  explanation: '',
  xp_reward: 5,
  order: 0
});

const filteredLessons = computed(() => {
  if (!selectedCourseId.value) return allLessons.value;
  return allLessons.value.filter(l => l.course_id == selectedCourseId.value);
});

const filteredExercises = computed(() => {
  let filtered = exercises.value;
  if (selectedLessonId.value) {
    filtered = filtered.filter(e => e.lesson_id == selectedLessonId.value);
  }
  return filtered;
});

const getCourseName = (courseId) => {
  const course = courses.value.find(c => c.id == courseId);
  return course ? `${course.flag_icon} ${course.name}` : 'Unknown';
};

const getLessonName = (lessonId) => {
  const lesson = allLessons.value.find(l => l.id == lessonId);
  return lesson ? `${lesson.icon} ${lesson.title}` : 'Unknown';
};

const getTypeClass = (type) => {
  const classes = {
    multiple_choice: 'bg-blue-100 text-blue-800',
    translate: 'bg-green-100 text-green-800',
    speak: 'bg-purple-100 text-purple-800',
    listen: 'bg-orange-100 text-orange-800',
    match_pairs: 'bg-pink-100 text-pink-800',
    fill_blank: 'bg-yellow-100 text-yellow-800',
    word_order: 'bg-indigo-100 text-indigo-800'
  };
  return classes[type] || classes.multiple_choice;
};

const fetchData = async () => {
  loading.value = true;
  // Mock data
  courses.value = [
    { id: 1, name: 'English for Beginners', flag_icon: '🇬🇧' },
    { id: 2, name: 'Spanish Basics', flag_icon: '🇪🇸' }
  ];

  allLessons.value = [
    { id: 1, course_id: 1, title: 'Greetings', icon: '👋' },
    { id: 2, course_id: 1, title: 'Numbers', icon: '🔢' },
    { id: 3, course_id: 2, title: 'Hola!', icon: '👋' }
  ];

  exercises.value = [
    { id: 1, lesson_id: 1, type: 'multiple_choice', question: 'How do you say "Hello"?', options: '["Hello","Goodbye","Thanks","Please"]', correct_answer: 'Hello', explanation: 'Hello is the common greeting', xp_reward: 5, order: 1 },
    { id: 2, lesson_id: 1, type: 'translate', question: 'Translate: مرحبا', options: null, correct_answer: 'Hello', explanation: 'مرحبا means Hello', xp_reward: 5, order: 2 },
    { id: 3, lesson_id: 2, type: 'multiple_choice', question: 'What comes after 5?', options: '["4","6","7","5"]', correct_answer: '6', explanation: 'The sequence is 5, 6, 7', xp_reward: 5, order: 1 }
  ];
  loading.value = false;
};

const onCourseChange = () => {
  selectedLessonId.value = '';
};

const openCreateModal = () => {
  editingExercise.value = null;
  optionsText.value = '';
  form.value = {
    lesson_id: '',
    type: 'multiple_choice',
    question: '',
    options: null,
    correct_answer: '',
    explanation: '',
    xp_reward: 5,
    order: exercises.value.length
  };
  showModal.value = true;
};

const editExercise = (exercise) => {
  editingExercise.value = exercise;
  form.value = { ...exercise };
  if (exercise.options) {
    optionsText.value = JSON.parse(exercise.options).join(', ');
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingExercise.value = null;
};

const saveExercise = () => {
  const exerciseData = { ...form.value };
  if (form.value.type === 'multiple_choice' && optionsText.value) {
    exerciseData.options = JSON.stringify(optionsText.value.split(',').map(o => o.trim()));
  }
  
  if (editingExercise.value) {
    const index = exercises.value.findIndex(e => e.id === editingExercise.value.id);
    exercises.value[index] = { ...exerciseData, id: editingExercise.value.id };
  } else {
    exercises.value.push({ ...exerciseData, id: Date.now() });
  }
  closeModal();
};

const deleteExercise = (exercise) => {
  if (confirm(`Delete this exercise?`)) {
    exercises.value = exercises.value.filter(e => e.id !== exercise.id);
  }
};

onMounted(() => {
  fetchData();
});
</script>
