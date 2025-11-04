import AsyncStorage from '@react-native-async-storage/async-storage';

// API Configuration
// IMPORTANT: Hardcoded for development - change this for production
const API_URL = 'http://10.50.240.89/parfumes/backend/public/api';

// Debug: Log the API URL being used
console.log('🔧 API_URL configured:', API_URL);
console.log('🔧 Environment variable:', process.env.EXPO_PUBLIC_API_URL);

/**
 * API Client for Laravel Backend
 */
class ApiClient {
  constructor() {
    this.token = null;
    this.loadToken();
  }

  async loadToken() {
    this.token = await AsyncStorage.getItem('auth_token');
  }

  async saveToken(token) {
    this.token = token;
    await AsyncStorage.setItem('auth_token', token);
  }

  async clearToken() {
    this.token = null;
    await AsyncStorage.removeItem('auth_token');
  }

  async request(endpoint, options = {}) {
    try {
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers,
      };

      if (this.token) {
        headers['Authorization'] = `Bearer ${this.token}`;
      }

      const url = `${API_URL}${endpoint}`;
      console.log('API Request:', options.method || 'GET', url);

      const response = await fetch(url, {
        ...options,
        headers,
      });

      const data = await response.json();

      if (!response.ok) {
        console.error('API Error Response:', data);
        throw new Error(data.error || data.message || 'حدث خطأ غير متوقع');
      }

      return data;
    } catch (error) {
      console.error('API Request Failed:', error.message);
      // If it's a network error, provide a more specific message
      if (error.message === 'Network request failed' || error.message.includes('fetch')) {
        throw new Error('فشل الاتصال بالخادم. تأكد من اتصالك بالإنترنت');
      }
      throw error;
    }
  }

  // ==================== AUTH METHODS ====================

  async register(email, password, fullName, phoneNumber) {
    try {
      console.log('API: Registering user...', { email, fullName, phoneNumber });
      
      const data = await this.request('/register', {
        method: 'POST',
        body: JSON.stringify({
          email,
          password,
          full_name: fullName,
          phone_number: phoneNumber,
        }),
      });

      console.log('API: Registration successful', data);
      
      if (data.token) {
        await this.saveToken(data.token);
      }
      
      return { user: data.user, error: null };
    } catch (error) {
      console.error('API: Registration failed', error);
      return { user: null, error: { message: error.message } };
    }
  }

  async login(email, password) {
    try {
      const data = await this.request('/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
      });

      await this.saveToken(data.token);
      return { user: data.user, error: null };
    } catch (error) {
      return { user: null, error: { message: error.message } };
    }
  }

  async logout() {
    try {
      await this.request('/logout', { method: 'POST' });
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      await this.clearToken();
    }
  }

  async getUser() {
    try {
      return await this.request('/user');
    } catch (error) {
      return null;
    }
  }

  async updateProfile(data) {
    return await this.request('/user/profile', {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  async changePassword(currentPassword, newPassword) {
    return await this.request('/user/password', {
      method: 'PUT',
      body: JSON.stringify({
        current_password: currentPassword,
        new_password: newPassword,
      }),
    });
  }

  // ==================== PROPERTY METHODS ====================

  async getProperties(params = {}) {
    const queryString = Object.keys(params).length 
      ? '?' + new URLSearchParams(params).toString() 
      : '';
    return await this.request(`/properties${queryString}`);
  }

  async getProperty(id) {
    return await this.request(`/properties/${id}`);
  }

  async createProperty(property) {
    return await this.request('/properties', {
      method: 'POST',
      body: JSON.stringify(property),
    });
  }

  async updateProperty(id, property) {
    return await this.request(`/properties/${id}`, {
      method: 'PUT',
      body: JSON.stringify(property),
    });
  }

  async deleteProperty(id) {
    return await this.request(`/properties/${id}`, {
      method: 'DELETE',
    });
  }

  async getUserProperties(page, perPage) {
    const params = new URLSearchParams();
    if (page) params.append('page', page.toString());
    if (perPage) params.append('per_page', perPage.toString());
    
    return await this.request(`/user/properties?${params.toString()}`);
  }

  // ==================== FAVORITE METHODS ====================

  async getFavorites(page, perPage) {
    const params = new URLSearchParams();
    if (page) params.append('page', page.toString());
    if (perPage) params.append('per_page', perPage.toString());
    
    return await this.request(`/favorites?${params.toString()}`);
  }

  async addFavorite(propertyId) {
    return await this.request(`/favorites/${propertyId}`, {
      method: 'POST',
    });
  }

  async removeFavorite(propertyId) {
    return await this.request(`/favorites/${propertyId}`, {
      method: 'DELETE',
    });
  }

  async checkFavorite(propertyId) {
    return await this.request(`/favorites/${propertyId}/check`);
  }

  // ==================== UPLOAD METHODS ====================

  async uploadImage(imageUri) {
    const formData = new FormData();
    
    const filename = imageUri.split('/').pop() || 'image.jpg';
    const match = /\.(\w+)$/.exec(filename);
    const type = match ? `image/${match[1]}` : 'image/jpeg';

    formData.append('image', {
      uri: imageUri,
      name: filename,
      type,
    });

    const headers = {
      'Accept': 'application/json',
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    const response = await fetch(`${API_URL}/upload`, {
      method: 'POST',
      headers,
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || 'فشل رفع الصورة');
    }

    return data;
  }

  async uploadMultipleImages(imageUris) {
    const formData = new FormData();

    imageUris.forEach((uri, index) => {
      const filename = uri.split('/').pop() || `image${index}.jpg`;
      const match = /\.(\w+)$/.exec(filename);
      const type = match ? `image/${match[1]}` : 'image/jpeg';

      formData.append('images[]', {
        uri,
        name: filename,
        type,
      });
    });

    const headers = {
      'Accept': 'application/json',
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    const response = await fetch(`${API_URL}/upload/multiple`, {
      method: 'POST',
      headers,
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || 'فشل رفع الصور');
    }

    return data;
  }
}

export const api = new ApiClient();
