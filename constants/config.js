// App Configuration
export const APP_NAME = 'Parfumes';
export const API_URL = process.env.EXPO_PUBLIC_API_URL || 'http://localhost:8000/api';

// Property Categories
export const PROPERTY_CATEGORIES = [
  { value: 'apartment', label: 'شقة', icon: 'building' },
  { value: 'house', label: 'منزل', icon: 'home' },
  { value: 'villa', label: 'فيلا', icon: 'castle' },
  { value: 'land', label: 'أرض', icon: 'map' },
  { value: 'commercial', label: 'تجاري', icon: 'store' },
  { value: 'farm', label: 'مزرعة', icon: 'tractor' },
];

// Property Status
export const PROPERTY_STATUS = {
  PENDING: 'pending',
  APPROVED: 'approved',
  REJECTED: 'rejected',
};

// Price Units
export const PRICE_UNITS = [
  { value: 'tnd', label: 'دينار' },
  { value: 'tnd/m²', label: 'دينار/م²' },
  { value: 'tnd/hectare', label: 'دينار/هكتار' },
];

// Size Units
export const SIZE_UNITS = [
  { value: 'm²', label: 'متر مربع' },
  { value: 'hectare', label: 'هكتار' },
];

// Validation Rules
export const VALIDATION = {
  MIN_PASSWORD_LENGTH: 6,
  MAX_TITLE_LENGTH: 100,
  MAX_DESCRIPTION_LENGTH: 1000,
  MAX_IMAGES: 10,
  MIN_PRICE: 0,
  MIN_SIZE: 0,
};

// Pagination
export const ITEMS_PER_PAGE = 20;

// Image Upload
export const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
export const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];
