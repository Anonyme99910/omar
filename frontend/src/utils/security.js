/**
 * Security utilities for XSS protection and input sanitization
 */

/**
 * Escape HTML to prevent XSS attacks
 * @param {string} text - Text to escape
 * @returns {string} - Escaped text
 */
export function escapeHtml(text) {
  if (typeof text !== 'string') return text
  
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '/': '&#x2F;',
  }
  
  return text.replace(/[&<>"'/]/g, (char) => map[char])
}

/**
 * Sanitize user input to prevent XSS
 * @param {string} input - User input
 * @returns {string} - Sanitized input
 */
export function sanitizeInput(input) {
  if (typeof input !== 'string') return input
  
  // Remove script tags
  let sanitized = input.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
  
  // Remove event handlers
  sanitized = sanitized.replace(/on\w+\s*=\s*["'][^"']*["']/gi, '')
  
  // Remove javascript: protocol
  sanitized = sanitized.replace(/javascript:/gi, '')
  
  // Remove data: protocol (can be used for XSS)
  sanitized = sanitized.replace(/data:text\/html/gi, '')
  
  return sanitized
}

/**
 * Validate and sanitize URL
 * @param {string} url - URL to validate
 * @returns {string|null} - Sanitized URL or null if invalid
 */
export function sanitizeUrl(url) {
  if (typeof url !== 'string') return null
  
  // Allow only http, https, and relative URLs
  const allowedProtocols = /^(https?:\/\/|\/)/i
  
  if (!allowedProtocols.test(url)) {
    return null
  }
  
  // Remove javascript: and data: protocols
  if (/^(javascript|data):/i.test(url)) {
    return null
  }
  
  return url
}

/**
 * Sanitize object recursively
 * @param {Object} obj - Object to sanitize
 * @returns {Object} - Sanitized object
 */
export function sanitizeObject(obj) {
  if (typeof obj !== 'object' || obj === null) {
    return obj
  }
  
  if (Array.isArray(obj)) {
    return obj.map(item => sanitizeObject(item))
  }
  
  const sanitized = {}
  for (const key in obj) {
    if (obj.hasOwnProperty(key)) {
      const value = obj[key]
      if (typeof value === 'string') {
        sanitized[key] = sanitizeInput(value)
      } else if (typeof value === 'object') {
        sanitized[key] = sanitizeObject(value)
      } else {
        sanitized[key] = value
      }
    }
  }
  
  return sanitized
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} - True if valid
 */
export function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Validate phone number (Egyptian format)
 * @param {string} phone - Phone number to validate
 * @returns {boolean} - True if valid
 */
export function isValidPhone(phone) {
  // Egyptian phone: 01xxxxxxxxx (11 digits)
  const phoneRegex = /^01[0-9]{9}$/
  return phoneRegex.test(phone.replace(/\s/g, ''))
}

/**
 * Strip HTML tags from text
 * @param {string} html - HTML text
 * @returns {string} - Plain text
 */
export function stripHtml(html) {
  if (typeof html !== 'string') return html
  
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}

/**
 * Validate SQL-safe string (no SQL injection patterns)
 * @param {string} input - Input to validate
 * @returns {boolean} - True if safe
 */
export function isSqlSafe(input) {
  if (typeof input !== 'string') return true
  
  // Check for common SQL injection patterns
  const sqlPatterns = [
    /(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b)/i,
    /(--|\#|\/\*|\*\/)/,
    /(\bOR\b.*=.*)/i,
    /(\bAND\b.*=.*)/i,
    /(;|\||&)/,
    /(UNION.*SELECT)/i,
  ]
  
  return !sqlPatterns.some(pattern => pattern.test(input))
}

/**
 * Create a safe display name (for user-generated content)
 * @param {string} name - Name to sanitize
 * @returns {string} - Safe name
 */
export function createSafeDisplayName(name) {
  if (typeof name !== 'string') return ''
  
  // Remove all HTML
  let safe = stripHtml(name)
  
  // Remove special characters except Arabic, English, numbers, and spaces
  safe = safe.replace(/[^\u0600-\u06FFa-zA-Z0-9\s]/g, '')
  
  // Limit length
  safe = safe.substring(0, 100)
  
  // Trim
  safe = safe.trim()
  
  return safe
}

/**
 * Validate and sanitize number input
 * @param {any} value - Value to validate
 * @param {Object} options - Validation options
 * @returns {number|null} - Sanitized number or null
 */
export function sanitizeNumber(value, options = {}) {
  const { min = -Infinity, max = Infinity, decimals = 2 } = options
  
  const num = parseFloat(value)
  
  if (isNaN(num)) return null
  if (num < min || num > max) return null
  
  return parseFloat(num.toFixed(decimals))
}

export default {
  escapeHtml,
  sanitizeInput,
  sanitizeUrl,
  sanitizeObject,
  isValidEmail,
  isValidPhone,
  stripHtml,
  isSqlSafe,
  createSafeDisplayName,
  sanitizeNumber
}
