<template>
  <div class="import-container">
    <div class="header">
      <h1>استيراد المنتجات</h1>
      <p class="subtitle">استيراد المنتجات بشكل جماعي عبر Excel أو SQL</p>
    </div>

    <!-- Import Method Tabs -->
    <div class="tabs">
      <button 
        :class="['tab', { active: activeTab === 'excel' }]"
        @click="activeTab = 'excel'"
      >
        📊 استيراد Excel/CSV
      </button>
      <button 
        :class="['tab', { active: activeTab === 'sql' }]"
        @click="activeTab = 'sql'"
      >
        💾 استيراد SQL
      </button>
      <button 
        :class="['tab', { active: activeTab === 'guide' }]"
        @click="activeTab = 'guide'"
      >
        📖 دليل الاستخدام
      </button>
    </div>

    <!-- Excel Import Tab -->
    <div v-if="activeTab === 'excel'" class="tab-content">
      <div class="card">
        <h2>استيراد من Excel/CSV</h2>
        
        <!-- Download Template -->
        <div class="template-section">
          <p>قم بتحميل القالب أولاً وملء البيانات</p>
          <button @click="downloadTemplate" class="btn-secondary" :disabled="downloading">
            <span v-if="!downloading">⬇️ تحميل قالب Excel</span>
            <span v-else>جاري التحميل...</span>
          </button>
        </div>

        <!-- File Upload -->
        <div class="upload-section">
          <div 
            class="dropzone"
            :class="{ 'dragover': isDragging }"
            @dragover.prevent="isDragging = true"
            @dragleave="isDragging = false"
            @drop.prevent="handleFileDrop"
            @click="$refs.fileInput.click()"
          >
            <input 
              ref="fileInput"
              type="file"
              accept=".xlsx,.xls,.csv"
              @change="handleFileSelect"
              style="display: none"
            >
            
            <div v-if="!selectedFile" class="dropzone-content">
              <div class="upload-icon">📁</div>
              <p>اسحب وأفلت ملف Excel هنا</p>
              <p class="or">أو</p>
              <button class="btn-primary">اختر ملف</button>
              <p class="file-types">يدعم: .xlsx, .xls, .csv</p>
            </div>

            <div v-else class="file-selected">
              <div class="file-icon">📄</div>
              <div class="file-info">
                <p class="file-name">{{ selectedFile.name }}</p>
                <p class="file-size">{{ formatFileSize(selectedFile.size) }}</p>
              </div>
              <button @click.stop="removeFile" class="btn-remove">✕</button>
            </div>
          </div>

          <button 
            v-if="selectedFile"
            @click="uploadExcel"
            class="btn-primary btn-upload"
            :disabled="uploading"
          >
            <span v-if="!uploading">⬆️ رفع واستيراد</span>
            <span v-else>جاري الاستيراد... {{ uploadProgress }}%</span>
          </button>
        </div>

        <!-- Import Results -->
        <div v-if="importResults" class="results-section">
          <div class="results-card" :class="importResults.success ? 'success' : 'error'">
            <h3>{{ importResults.success ? '✅ نجح الاستيراد' : '❌ فشل الاستيراد' }}</h3>
            <div class="stats">
              <div class="stat">
                <span class="label">تم الاستيراد:</span>
                <span class="value">{{ importResults.data?.imported || 0 }}</span>
              </div>
              <div class="stat">
                <span class="label">تم التخطي:</span>
                <span class="value">{{ importResults.data?.skipped || 0 }}</span>
              </div>
            </div>
            
            <div v-if="importResults.data?.errors && importResults.data.errors.length > 0" class="errors">
              <h4>الأخطاء:</h4>
              <ul>
                <li v-for="(error, index) in importResults.data.errors.slice(0, 5)" :key="index">
                  {{ typeof error === 'string' ? error : error.error || JSON.stringify(error) }}
                </li>
                <li v-if="importResults.data.errors.length > 5">
                  ... و {{ importResults.data.errors.length - 5 }} خطأ آخر
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SQL Import Tab -->
    <div v-if="activeTab === 'sql'" class="tab-content">
      <div class="card">
        <h2>استيراد من SQL</h2>
        
        <!-- Get SQL Template -->
        <div class="template-section">
          <p>احصل على قالب SQL للبدء</p>
          <button @click="loadSQLTemplate" class="btn-secondary" :disabled="loadingTemplate">
            <span v-if="!loadingTemplate">📋 عرض قالب SQL</span>
            <span v-else">جاري التحميل...</span>
          </button>
        </div>

        <!-- SQL Editor -->
        <div class="sql-editor-section">
          <label>جمل SQL:</label>
          <textarea 
            v-model="sqlContent"
            class="sql-editor"
            placeholder="INSERT INTO products ..."
            rows="15"
          ></textarea>
          
          <button 
            @click="importSQL"
            class="btn-primary"
            :disabled="!sqlContent || importing"
          >
            <span v-if="!importing">⚡ تنفيذ واستيراد</span>
            <span v-else>جاري التنفيذ...</span>
          </button>
        </div>

        <!-- SQL Import Results -->
        <div v-if="sqlResults" class="results-section">
          <div class="results-card" :class="sqlResults.success ? 'success' : 'error'">
            <h3>{{ sqlResults.success ? '✅ نجح التنفيذ' : '❌ فشل التنفيذ' }}</h3>
            <div class="stats">
              <div class="stat">
                <span class="label">جمل تم تنفيذها:</span>
                <span class="value">{{ sqlResults.data?.imported || 0 }}</span>
              </div>
            </div>
            
            <div v-if="sqlResults.data?.errors && sqlResults.data.errors.length > 0" class="errors">
              <h4>الأخطاء:</h4>
              <ul>
                <li v-for="(error, index) in sqlResults.data.errors" :key="index">
                  <strong>{{ error.statement }}</strong><br>
                  {{ error.error }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Guide Tab -->
    <div v-if="activeTab === 'guide'" class="tab-content">
      <div class="card">
        <h2>📖 دليل الاستخدام</h2>
        
        <div v-if="guide" class="guide-content">
          <!-- Excel Guide -->
          <div class="guide-section">
            <h3>{{ guide.excel.title }}</h3>
            <div class="steps">
              <div v-for="(step, index) in guide.excel.steps" :key="index" class="step">
                <span class="step-number">{{ index + 1 }}</span>
                <span class="step-text">{{ step }}</span>
              </div>
            </div>
            
            <h4>الأعمدة المطلوبة:</h4>
            <table class="columns-table">
              <thead>
                <tr>
                  <th>اسم العمود</th>
                  <th>الوصف</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(desc, col) in guide.excel.required_columns" :key="col">
                  <td><code>{{ col }}</code></td>
                  <td>{{ desc }}</td>
                </tr>
              </tbody>
            </table>
            
            <h4>ملاحظات:</h4>
            <ul class="notes">
              <li v-for="(note, index) in guide.excel.notes" :key="index">{{ note }}</li>
            </ul>
          </div>

          <!-- SQL Guide -->
          <div class="guide-section">
            <h3>{{ guide.sql.title }}</h3>
            <div class="steps">
              <div v-for="(step, index) in guide.sql.steps" :key="index" class="step">
                <span class="step-number">{{ index + 1 }}</span>
                <span class="step-text">{{ step }}</span>
              </div>
            </div>
            
            <h4>قواعد الأمان:</h4>
            <ul class="security">
              <li v-for="(rule, index) in guide.sql.security" :key="index">🔒 {{ rule }}</li>
            </ul>
          </div>
        </div>
        
        <button v-else @click="loadGuide" class="btn-primary">تحميل الدليل</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()

const activeTab = ref('excel')
const selectedFile = ref(null)
const isDragging = ref(false)
const downloading = ref(false)
const uploading = ref(false)
const uploadProgress = ref(0)
const importing = ref(false)
const loadingTemplate = ref(false)
const importResults = ref(null)
const sqlResults = ref(null)
const sqlContent = ref('')
const guide = ref(null)

// Excel Import Functions
const downloadTemplate = async () => {
  try {
    downloading.value = true
    const response = await api.downloadProductTemplate()
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'products_template.csv')
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    toast.success('تم تحميل القالب بنجاح')
  } catch (error) {
    toast.error('فشل تحميل القالب')
    console.error(error)
  } finally {
    downloading.value = false
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    importResults.value = null
  }
}

const handleFileDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file) {
    selectedFile.value = file
    importResults.value = null
  }
}

const removeFile = () => {
  selectedFile.value = null
  importResults.value = null
}

const formatFileSize = (bytes) => {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB'
}

const uploadExcel = async () => {
  if (!selectedFile.value) return
  
  try {
    uploading.value = true
    uploadProgress.value = 0
    
    const formData = new FormData()
    formData.append('file', selectedFile.value)
    
    const response = await api.importProductsExcel(formData)
    
    uploadProgress.value = 100
    importResults.value = response.data
    
    if (response.data.success) {
      toast.success('تم استيراد المنتجات بنجاح!')
      selectedFile.value = null
    } else {
      toast.error('فشل استيراد بعض المنتجات')
    }
  } catch (error) {
    toast.error('فشل استيراد المنتجات')
    importResults.value = {
      success: false,
      message: error.response?.data?.message || error.message
    }
  } finally {
    uploading.value = false
  }
}

// SQL Import Functions
const loadSQLTemplate = async () => {
  try {
    loadingTemplate.value = true
    const response = await api.getSQLTemplate()
    sqlContent.value = response.data.template
    toast.success('تم تحميل القالب')
  } catch (error) {
    toast.error('فشل تحميل القالب')
  } finally {
    loadingTemplate.value = false
  }
}

const importSQL = async () => {
  if (!sqlContent.value) return
  
  try {
    importing.value = true
    const response = await api.importProductsSQL({ sql: sqlContent.value })
    
    sqlResults.value = response.data
    
    if (response.data.success) {
      toast.success('تم تنفيذ SQL بنجاح!')
      sqlContent.value = ''
    } else {
      toast.error('فشل تنفيذ بعض الجمل')
    }
  } catch (error) {
    toast.error('فشل تنفيذ SQL')
    sqlResults.value = {
      success: false,
      message: error.response?.data?.message || error.message
    }
  } finally {
    importing.value = false
  }
}

// Guide Functions
const loadGuide = async () => {
  try {
    const response = await api.getImportGuide()
    guide.value = response.data.guide
  } catch (error) {
    toast.error('فشل تحميل الدليل')
  }
}

// Load guide on mount
loadGuide()
</script>

<style scoped>
.import-container {
  padding: 20px;
  direction: rtl;
}

.header {
  margin-bottom: 20px;
}

.header h1 {
  font-size: 24px;
  color: #1e293b;
  margin-bottom: 5px;
}

.subtitle {
  color: #64748b;
  font-size: 14px;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  border-bottom: 2px solid #e2e8f0;
}

.tab {
  padding: 12px 24px;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-size: 15px;
  color: #64748b;
  transition: all 0.3s;
  font-weight: 500;
}

.tab.active {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
}

.tab:hover {
  color: #3b82f6;
}

.tab-content {
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card h2 {
  font-size: 20px;
  margin-bottom: 20px;
  color: #1e293b;
}

.template-section {
  background: #f8fafc;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 24px;
  text-align: center;
}

.template-section p {
  margin-bottom: 12px;
  color: #475569;
}

.upload-section {
  margin-bottom: 24px;
}

.dropzone {
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: #f8fafc;
}

.dropzone:hover {
  border-color: #3b82f6;
  background: #eff6ff;
}

.dropzone.dragover {
  border-color: #3b82f6;
  background: #dbeafe;
}

.dropzone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.upload-icon {
  font-size: 48px;
}

.or {
  color: #94a3b8;
  font-size: 14px;
}

.file-types {
  color: #94a3b8;
  font-size: 12px;
  margin-top: 8px;
}

.file-selected {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: white;
  border-radius: 8px;
}

.file-icon {
  font-size: 32px;
}

.file-info {
  flex: 1;
  text-align: right;
}

.file-name {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.file-size {
  color: #64748b;
  font-size: 14px;
}

.btn-remove {
  background: #fee2e2;
  color: #dc2626;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
  transition: all 0.2s;
}

.btn-remove:hover {
  background: #fecaca;
}

.btn-upload {
  width: 100%;
  margin-top: 16px;
  padding: 14px;
  font-size: 16px;
}

.sql-editor-section {
  margin-top: 20px;
}

.sql-editor-section label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #1e293b;
}

.sql-editor {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-family: 'Courier New', monospace;
  font-size: 14px;
  resize: vertical;
  margin-bottom: 16px;
}

.sql-editor:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.results-section {
  margin-top: 24px;
}

.results-card {
  padding: 20px;
  border-radius: 8px;
  border-right: 4px solid;
}

.results-card.success {
  background: #f0fdf4;
  border-color: #22c55e;
}

.results-card.error {
  background: #fef2f2;
  border-color: #ef4444;
}

.results-card h3 {
  margin-bottom: 16px;
  font-size: 18px;
}

.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
  margin-bottom: 16px;
}

.stat {
  display: flex;
  justify-content: space-between;
  padding: 12px;
  background: white;
  border-radius: 6px;
}

.stat .label {
  color: #64748b;
  font-size: 14px;
}

.stat .value {
  font-weight: 700;
  font-size: 18px;
  color: #1e293b;
}

.errors {
  background: white;
  padding: 16px;
  border-radius: 6px;
  margin-top: 16px;
}

.errors h4 {
  color: #dc2626;
  margin-bottom: 12px;
}

.errors ul {
  list-style: none;
  padding: 0;
}

.errors li {
  padding: 8px;
  margin-bottom: 8px;
  background: #fef2f2;
  border-radius: 4px;
  font-size: 14px;
  color: #991b1b;
}

.guide-content {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.guide-section h3 {
  font-size: 18px;
  margin-bottom: 16px;
  color: #1e293b;
}

.guide-section h4 {
  font-size: 16px;
  margin: 20px 0 12px;
  color: #475569;
}

.steps {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.step {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
}

.step-number {
  background: #3b82f6;
  color: white;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  flex-shrink: 0;
}

.step-text {
  color: #1e293b;
  font-size: 14px;
}

.columns-table {
  width: 100%;
  border-collapse: collapse;
  margin: 12px 0;
}

.columns-table th,
.columns-table td {
  padding: 12px;
  text-align: right;
  border-bottom: 1px solid #e2e8f0;
}

.columns-table th {
  background: #f8fafc;
  font-weight: 600;
  color: #475569;
}

.columns-table code {
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  color: #3b82f6;
}

.notes,
.security {
  list-style: none;
  padding: 0;
}

.notes li,
.security li {
  padding: 8px 12px;
  margin-bottom: 8px;
  background: #eff6ff;
  border-radius: 6px;
  color: #1e40af;
}

.security li {
  background: #fef3c7;
  color: #92400e;
}

.btn-primary,
.btn-secondary {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-primary:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn-secondary:hover:not(:disabled) {
  background: #e2e8f0;
}

.btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
