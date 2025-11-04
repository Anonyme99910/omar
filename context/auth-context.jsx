import React, { createContext, useState, useContext, useEffect } from 'react';
import { api } from '../lib/api';
import AsyncStorage from '@react-native-async-storage/async-storage';

const AuthContext = createContext({
  user: null,
  profile: null,
  isAuthenticated: false,
  isLoading: true,
  login: async () => ({ error: null }),
  register: async () => ({ error: null }),
  logout: async () => {},
  refreshProfile: async () => {},
});

export const useAuthContext = () => useContext(AuthContext);
export const useAuth = () => useContext(AuthContext); // Alias for convenience

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [profile, setProfile] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  // Function to restore session from storage
  const restoreSession = async () => {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      
      if (token) {
        const userData = await api.getUser();
        if (userData) {
          setUser(userData);
          setProfile(userData);
        }
      }
    } catch (error) {
      console.error('Error restoring session:', error);
      await AsyncStorage.removeItem('auth_token');
      setUser(null);
      setProfile(null);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    restoreSession();
  }, []);

  async function login(email, password) {
    try {
      const { user: userData, error } = await api.login(email, password);
      
      if (error) {
        return { 
          error: { 
            message: error.message || 'حدث خطأ أثناء تسجيل الدخول'
          }
        };
      }

      if (userData) {
        setUser(userData);
        setProfile(userData);
      }

      return { error: null };
    } catch (error) {
      return { 
        error: { 
          message: error.message || 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى'
        }
      };
    }
  }

  async function register(email, password, fullName, phoneNumber) {
    try {
      // Validate input
      if (!email || !password || !fullName || !phoneNumber) {
        return {
          error: {
            message: 'جميع الحقول مطلوبة'
          }
        };
      }

      // Check if email is valid
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        return {
          error: {
            message: 'البريد الإلكتروني غير صالح'
          }
        };
      }

      // Check if password is strong enough
      if (password.length < 6) {
        return {
          error: {
            message: 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
          }
        };
      }

      const { user: userData, error } = await api.register(email, password, fullName, phoneNumber);

      if (error) {
        return {
          error: {
            message: error.message || 'حدث خطأ أثناء إنشاء الحساب'
          }
        };
      }

      if (userData) {
        setUser(userData);
        setProfile(userData);
      }

      return { error: null };
    } catch (error) {
      console.error('Unexpected error during registration:', error);
      return {
        error: {
          message: error.message || 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى'
        }
      };
    }
  }

  async function logout() {
    try {
      await api.logout();
      setUser(null);
      setProfile(null);
    } catch (error) {
      console.error('Error logging out:', error);
    }
  }

  async function refreshProfile() {
    try {
      const userData = await api.getUser();
      if (userData) {
        setUser(userData);
        setProfile(userData);
      }
    } catch (error) {
      console.error('Error refreshing profile:', error);
    }
  }

  return (
    <AuthContext.Provider
      value={{
        user,
        profile,
        isAuthenticated: !!user,
        isLoading,
        login,
        register,
        logout,
        refreshProfile,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};
