// Convert Arabic numerals to Latin numerals
export const toLatinNumbers = (str) => {
  if (str === null || str === undefined) return '0'
  const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩']
  const latinNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
  
  return String(str).split('').map(char => {
    const index = arabicNumbers.indexOf(char)
    return index !== -1 ? latinNumbers[index] : char
  }).join('')
}

// Format currency with Latin numerals
export const formatCurrencyLatin = (value) => {
  if (value === null || value === undefined) value = 0
  
  // Use en-US locale to ensure Latin digits
  const formatted = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
  
  // Convert any remaining Arabic numerals to Latin
  return toLatinNumbers(formatted)
}
