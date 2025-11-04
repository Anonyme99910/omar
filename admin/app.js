const { createApp } = Vue;

// API Configuration
const API_URL = 'http://localhost/parfumes/backend/public/api';

// API Service
const api = {
    async request(endpoint, options = {}) {
        const token = localStorage.getItem('admin_token');
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        try {
            const response = await axios({
                url: `${API_URL}${endpoint}`,
                method: options.method || 'GET',
                data: options.data,
                params: options.params,
                headers,
                withCredentials: false
            });
            return response.data;
        } catch (error) {
            if (error.response?.status === 401) {
                localStorage.removeItem('admin_token');
                window.location.reload();
            }
            console.error('API Error:', error.response?.data || error.message);
            throw error;
        }
    },
    
    // Auth
    async login(email, password) {
        try {
            const data = await this.request('/login', {
                method: 'POST',
                data: { email, password }
            });
            if (data && data.user && data.user.is_admin) {
                localStorage.setItem('admin_token', data.token);
                return data;
            }
            throw new Error('غير مصرح لك بالدخول');
        } catch (error) {
            console.error('Login API error:', error);
            throw error;
        }
    },
    
    async logout() {
        await this.request('/logout', { method: 'POST' });
        localStorage.removeItem('admin_token');
    },
    
    // Dashboard
    async getDashboard() {
        return await this.request('/admin/dashboard');
    },
    
    // Users
    async getUsers(params = {}) {
        const query = new URLSearchParams(params).toString();
        return await this.request(`/admin/users?${query}`);
    },
    
    async toggleUserStatus(userId) {
        return await this.request(`/admin/users/${userId}/toggle-status`, {
            method: 'PUT'
        });
    },
    
    // Properties
    async getProperties(params = {}) {
        const query = new URLSearchParams(params).toString();
        return await this.request(`/admin/properties?${query}`);
    },
    
    async updatePropertyStatus(propertyId, status) {
        return await this.request(`/admin/properties/${propertyId}/status`, {
            method: 'PUT',
            data: { status }
        });
    },
    
    async deleteProperty(propertyId) {
        return await this.request(`/admin/properties/${propertyId}`, {
            method: 'DELETE'
        });
    }
};

// Main App
createApp({
    data() {
        return {
            currentPage: 'login',
            isAuthenticated: false,
            user: null,
            loading: false,
            
            // Login
            loginForm: {
                email: 'admin@parfumes.com',
                password: 'Admin@123'
            },
            loginError: '',
            
            // Dashboard
            stats: {},
            
            // Users
            users: [],
            usersPagination: {},
            usersFilters: {
                search: '',
                is_active: ''
            },
            
            // Properties
            properties: [],
            propertiesPagination: {},
            propertiesFilters: {
                search: '',
                status: 'pending',
                category: ''
            }
        };
    },
    
    mounted() {
        this.checkAuth();
    },
    
    methods: {
        // Auth Methods
        checkAuth() {
            const token = localStorage.getItem('admin_token');
            if (token) {
                this.isAuthenticated = true;
                this.currentPage = 'dashboard';
                this.loadDashboard();
            }
        },
        
        async login() {
            this.loginError = '';
            this.loading = true;
            
            try {
                const data = await api.login(this.loginForm.email, this.loginForm.password);
                this.user = data.user;
                this.isAuthenticated = true;
                this.currentPage = 'dashboard';
                await this.loadDashboard();
            } catch (error) {
                console.error('Login error:', error);
                if (error.response?.data?.message) {
                    this.loginError = error.response.data.message;
                } else if (error.response?.status === 419) {
                    this.loginError = 'خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى';
                } else if (error.response?.status === 422) {
                    this.loginError = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
                } else {
                    this.loginError = error.message || 'فشل تسجيل الدخول';
                }
            } finally {
                this.loading = false;
            }
        },
        
        async logout() {
            try {
                await api.logout();
            } catch (error) {
                console.error('Logout error:', error);
            }
            this.isAuthenticated = false;
            this.user = null;
            this.currentPage = 'login';
        },
        
        // Navigation
        navigateTo(page) {
            this.currentPage = page;
            
            if (page === 'dashboard') {
                this.loadDashboard();
            } else if (page === 'users') {
                this.loadUsers();
            } else if (page === 'properties') {
                this.loadProperties();
            }
        },
        
        // Dashboard Methods
        async loadDashboard() {
            this.loading = true;
            try {
                this.stats = await api.getDashboard();
            } catch (error) {
                console.error('Failed to load dashboard:', error);
            } finally {
                this.loading = false;
            }
        },
        
        // Users Methods
        async loadUsers(page = 1) {
            this.loading = true;
            console.log('Loading users, page:', page);
            try {
                // Build params, excluding empty values
                const params = { page, per_page: 20 };
                
                if (this.usersFilters.search && this.usersFilters.search.trim()) {
                    params.search = this.usersFilters.search.trim();
                }
                
                if (this.usersFilters.is_active !== '') {
                    params.is_active = this.usersFilters.is_active;
                }
                
                console.log('Users API params:', params);
                const response = await api.getUsers(params);
                console.log('Users API response:', response);
                this.users = response.data || [];
                this.usersPagination = {
                    current_page: response.current_page || 1,
                    last_page: response.last_page || 1,
                    total: response.total || 0
                };
                console.log('Users loaded:', this.users.length);
            } catch (error) {
                console.error('Failed to load users:', error);
                this.users = [];
            } finally {
                this.loading = false;
            }
        },
        
        async toggleUserStatus(user) {
            if (!confirm(`هل تريد ${user.is_active ? 'تعطيل' : 'تفعيل'} هذا المستخدم؟`)) {
                return;
            }
            
            try {
                await api.toggleUserStatus(user.id);
                user.is_active = !user.is_active;
            } catch (error) {
                alert('فشل في تحديث حالة المستخدم');
            }
        },
        
        // Properties Methods
        async loadProperties(page = 1) {
            this.loading = true;
            console.log('Loading properties, page:', page);
            try {
                // Build params, excluding empty values
                const params = { page, per_page: 12 };
                
                if (this.propertiesFilters.search && this.propertiesFilters.search.trim()) {
                    params.search = this.propertiesFilters.search.trim();
                }
                
                if (this.propertiesFilters.status && this.propertiesFilters.status !== '') {
                    params.status = this.propertiesFilters.status;
                }
                
                if (this.propertiesFilters.category && this.propertiesFilters.category !== '') {
                    params.category = this.propertiesFilters.category;
                }
                
                console.log('Properties API params:', params);
                const response = await api.getProperties(params);
                console.log('Properties API response:', response);
                this.properties = response.data || [];
                this.propertiesPagination = {
                    current_page: response.current_page || 1,
                    last_page: response.last_page || 1,
                    total: response.total || 0
                };
                console.log('Properties loaded:', this.properties.length);
            } catch (error) {
                console.error('Failed to load properties:', error);
                this.properties = [];
            } finally {
                this.loading = false;
            }
        },
        
        async updatePropertyStatus(property, status) {
            const statusText = status === 'approved' ? 'قبول' : 'رفض';
            if (!confirm(`هل تريد ${statusText} هذا العقار؟`)) {
                return;
            }
            
            try {
                await api.updatePropertyStatus(property.id, status);
                property.status = status;
            } catch (error) {
                alert('فشل في تحديث حالة العقار');
            }
        },
        
        async deleteProperty(property) {
            if (!confirm('هل تريد حذف هذا العقار نهائياً؟')) {
                return;
            }
            
            try {
                await api.deleteProperty(property.id);
                this.properties = this.properties.filter(p => p.id !== property.id);
            } catch (error) {
                alert('فشل في حذف العقار');
            }
        },
        
        // Helper Methods
        getStatusText(status) {
            const statusMap = {
                pending: 'قيد الانتظار',
                approved: 'معتمد',
                rejected: 'مرفوض'
            };
            return statusMap[status] || status;
        },
        
        getStatusClass(status) {
            const classMap = {
                pending: 'bg-yellow-100 text-yellow-800',
                approved: 'bg-green-100 text-green-800',
                rejected: 'bg-red-100 text-red-800'
            };
            return classMap[status] || '';
        },
        
        getImageUrl(image) {
            if (image.startsWith('http')) {
                return image;
            }
            return `http://localhost/parfumes/backend/public/storage/${image}`;
        }
    },
    
    template: `
        <div class="min-h-screen">
            <!-- Login Page -->
            <div v-if="!isAuthenticated && currentPage === 'login'" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brown to-brown-light">
                <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
                    <div class="text-center mb-8">
                        <h1 class="text-4xl font-bold text-brown mb-2">🌸 Parfumes</h1>
                        <p class="text-gray-600">لوحة التحكم الإدارية</p>
                    </div>
                    
                    <form @submit.prevent="login" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                البريد الإلكتروني
                            </label>
                            <input
                                v-model="loginForm.email"
                                type="email"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brown focus:border-transparent"
                                placeholder="admin@parfumes.com"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                كلمة المرور
                            </label>
                            <input
                                v-model="loginForm.password"
                                type="password"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brown focus:border-transparent"
                                placeholder="••••••••"
                            />
                        </div>
                        
                        <div v-if="loginError" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm text-right">
                            {{ loginError }}
                        </div>
                        
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full bg-brown text-white py-2 px-4 rounded-lg hover:bg-brown-dark transition-colors disabled:opacity-50"
                        >
                            <span v-if="loading">جاري تسجيل الدخول...</span>
                            <span v-else>تسجيل الدخول</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Admin Panel -->
            <div v-if="isAuthenticated">
                <!-- Header -->
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <h1 class="text-2xl font-bold text-brown">🌸 Parfumes</h1>
                            <nav class="flex space-x-2 space-x-reverse">
                                <button
                                    @click="navigateTo('dashboard')"
                                    :class="currentPage === 'dashboard' ? 'bg-brown text-white' : 'text-gray-600 hover:bg-gray-100'"
                                    class="px-4 py-2 rounded-lg transition-colors"
                                >
                                    📊 لوحة التحكم
                                </button>
                                <button
                                    @click="navigateTo('users')"
                                    :class="currentPage === 'users' ? 'bg-brown text-white' : 'text-gray-600 hover:bg-gray-100'"
                                    class="px-4 py-2 rounded-lg transition-colors"
                                >
                                    👥 المستخدمون
                                </button>
                                <button
                                    @click="navigateTo('properties')"
                                    :class="currentPage === 'properties' ? 'bg-brown text-white' : 'text-gray-600 hover:bg-gray-100'"
                                    class="px-4 py-2 rounded-lg transition-colors"
                                >
                                    🏠 العقارات
                                </button>
                            </nav>
                        </div>
                        <button @click="logout" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            تسجيل الخروج
                        </button>
                    </div>
                </header>
                
                <!-- Main Content -->
                <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Loading State -->
                    <div v-if="loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-brown"></div>
                        <p class="mt-4 text-gray-600">جاري التحميل...</p>
                    </div>
                    
                    <!-- Dashboard Page -->
                    <div v-else-if="currentPage === 'dashboard'">
                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm">إجمالي المستخدمين</p>
                                        <p class="text-3xl font-bold mt-2">{{ stats.total_users || 0 }}</p>
                                    </div>
                                    <div class="text-5xl opacity-50">👥</div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm">إجمالي العقارات</p>
                                        <p class="text-3xl font-bold mt-2">{{ stats.total_properties || 0 }}</p>
                                    </div>
                                    <div class="text-5xl opacity-50">🏠</div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-yellow-100 text-sm">قيد الانتظار</p>
                                        <p class="text-3xl font-bold mt-2">{{ stats.pending_properties || 0 }}</p>
                                    </div>
                                    <div class="text-5xl opacity-50">⏳</div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-100 text-sm">معتمدة</p>
                                        <p class="text-3xl font-bold mt-2">{{ stats.approved_properties || 0 }}</p>
                                    </div>
                                    <div class="text-5xl opacity-50">✅</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <button @click="navigateTo('users')" class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow text-center">
                                <div class="text-5xl mb-4">👥</div>
                                <h3 class="text-xl font-bold text-gray-800">إدارة المستخدمين</h3>
                                <p class="text-gray-600 mt-2">عرض وإدارة جميع المستخدمين</p>
                            </button>
                            
                            <button @click="navigateTo('properties')" class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow text-center">
                                <div class="text-5xl mb-4">🏠</div>
                                <h3 class="text-xl font-bold text-gray-800">إدارة العقارات</h3>
                                <p class="text-gray-600 mt-2">الموافقة على العقارات وإدارتها</p>
                            </button>
                            
                            <div class="bg-brown text-white rounded-lg shadow-lg p-8 text-center">
                                <div class="text-5xl mb-4">📊</div>
                                <h3 class="text-xl font-bold">الإحصائيات</h3>
                                <p class="opacity-90 mt-2">تقارير وتحليلات مفصلة</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Users Page -->
                    <div v-else-if="currentPage === 'users'">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">👥 إدارة المستخدمين</h2>
                        
                        <!-- Filters -->
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <input
                                    v-model="usersFilters.search"
                                    @input="loadUsers()"
                                    type="text"
                                    placeholder="بحث بالاسم أو البريد..."
                                    class="px-4 py-2 border border-gray-300 rounded-lg"
                                />
                                <select v-model="usersFilters.is_active" @change="loadUsers()" class="px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">جميع المستخدمين</option>
                                    <option value="1">نشط</option>
                                    <option value="0">غير نشط</option>
                                </select>
                                <div class="text-gray-600 flex items-center">
                                    إجمالي: <span class="font-bold mr-2">{{ usersPagination.total || 0 }}</span> مستخدم
                                </div>
                            </div>
                        </div>
                        
                        <!-- Users Table -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-brown text-white flex items-center justify-center font-bold">
                                                    {{ user.full_name.charAt(0) }}
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ user.full_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.phone_number || '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-3 py-1 rounded-full text-sm font-medium">
                                                {{ user.is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button
                                                v-if="!user.is_admin"
                                                @click="toggleUserStatus(user)"
                                                :class="user.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                                                class="text-white px-3 py-1 rounded-lg transition-colors"
                                            >
                                                {{ user.is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Properties Page -->
                    <div v-else-if="currentPage === 'properties'">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">🏠 إدارة العقارات</h2>
                        
                        <!-- Filters -->
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <input
                                    v-model="propertiesFilters.search"
                                    @input="loadProperties()"
                                    type="text"
                                    placeholder="بحث بالعنوان..."
                                    class="px-4 py-2 border border-gray-300 rounded-lg"
                                />
                                <select v-model="propertiesFilters.status" @change="loadProperties()" class="px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="approved">معتمد</option>
                                    <option value="rejected">مرفوض</option>
                                </select>
                                <select v-model="propertiesFilters.category" @change="loadProperties()" class="px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">جميع الفئات</option>
                                    <option value="apartment">شقة</option>
                                    <option value="villa">فيلا</option>
                                    <option value="land">أرض</option>
                                </select>
                                <div class="text-gray-600 flex items-center">
                                    إجمالي: <span class="font-bold mr-2">{{ propertiesPagination.total || 0 }}</span> عقار
                                </div>
                            </div>
                        </div>
                        
                        <!-- Properties Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="property in properties" :key="property.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <!-- Image -->
                                <div class="relative h-48 bg-gray-200">
                                    <img
                                        v-if="property.images && property.images.length"
                                        :src="getImageUrl(property.images[0])"
                                        :alt="property.title"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-5xl">
                                        🏠
                                    </div>
                                    <div class="absolute top-2 right-2">
                                        <span :class="getStatusClass(property.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                            {{ getStatusText(property.status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ property.title }}</h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ property.description }}</p>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="mr-2">📍</span>
                                            {{ property.location }}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="mr-2">💰</span>
                                            {{ property.price }} {{ property.price_unit }}
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex gap-2">
                                        <button
                                            v-if="property.status === 'pending'"
                                            @click="updatePropertyStatus(property, 'approved')"
                                            class="flex-1 bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition-colors"
                                        >
                                            ✅ قبول
                                        </button>
                                        <button
                                            v-if="property.status === 'pending'"
                                            @click="updatePropertyStatus(property, 'rejected')"
                                            class="flex-1 bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition-colors"
                                        >
                                            ❌ رفض
                                        </button>
                                        <button
                                            @click="deleteProperty(property)"
                                            class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition-colors"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    `
}).mount('#app');
