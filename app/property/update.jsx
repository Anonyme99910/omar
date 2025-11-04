import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  StyleSheet,
  ScrollView,
  Alert,
  TouchableOpacity,
  Image,
  ActivityIndicator,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { router, useLocalSearchParams } from 'expo-router';
import * as ImagePicker from 'expo-image-picker';
import { Button } from '../../components/Button';
import { api } from '../../lib/api';
import { useProperty } from '../../hooks/useProperties';
import { Colors, Spacing, BorderRadius, FontSizes } from '../../constants/theme';
import { PROPERTY_CATEGORIES, PRICE_UNITS, SIZE_UNITS } from '../../constants/config';

export default function UpdatePropertyScreen() {
  const { id } = useLocalSearchParams();
  const { property, loading: loadingProperty } = useProperty(id);
  
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [priceUnit, setPriceUnit] = useState('tnd');
  const [location, setLocation] = useState('');
  const [size, setSize] = useState('');
  const [sizeUnit, setSizeUnit] = useState('m²');
  const [phoneNumber, setPhoneNumber] = useState('');
  const [category, setCategory] = useState('apartment');
  const [images, setImages] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (property) {
      setTitle(property.title);
      setDescription(property.description);
      setPrice(property.price.toString());
      setPriceUnit(property.price_unit);
      setLocation(property.location);
      setSize(property.size.toString());
      setSizeUnit(property.size_unit);
      setPhoneNumber(property.phone_number);
      setCategory(property.category);
      setImages(property.images || []);
    }
  }, [property]);

  const pickImages = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsMultipleSelection: true,
      quality: 0.8,
    });

    if (!result.canceled) {
      setImages([...images, ...result.assets.map(asset => asset.uri)]);
    }
  };

  const removeImage = (index) => {
    setImages(images.filter((_, i) => i !== index));
  };

  const handleUpdate = async () => {
    if (!title || !description || !price || !location || !size || !phoneNumber) {
      Alert.alert('خطأ', 'الرجاء ملء جميع الحقول المطلوبة');
      return;
    }

    try {
      setLoading(true);

      // Check if there are new images to upload
      const newImages = images.filter(img => img.startsWith('file://'));
      let imageUrls = images.filter(img => !img.startsWith('file://'));

      if (newImages.length > 0) {
        const { urls } = await api.uploadMultipleImages(newImages);
        imageUrls = [...imageUrls, ...urls];
      }

      // Update property
      await api.updateProperty(id, {
        title,
        description,
        price: parseFloat(price),
        price_unit: priceUnit,
        location,
        size: parseFloat(size),
        size_unit: sizeUnit,
        phone_number: phoneNumber,
        category,
        images: imageUrls,
      });

      Alert.alert('نجح', 'تم تحديث العقار بنجاح', [
        { text: 'حسناً', onPress: () => router.back() }
      ]);
    } catch (error) {
      Alert.alert('خطأ', error.message || 'فشل في تحديث العقار');
    } finally {
      setLoading(false);
    }
  };

  if (loadingProperty) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={Colors.primary} />
          <Text style={styles.loadingText}>جاري التحميل...</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>✏️ تعديل العقار</Text>
      </View>

      <ScrollView style={styles.scrollView} contentContainerStyle={styles.scrollContent}>
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>معلومات أساسية</Text>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>العنوان *</Text>
            <TextInput
              style={styles.input}
              placeholder="مثال: شقة فاخرة في تونس"
              value={title}
              onChangeText={setTitle}
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>الوصف *</Text>
            <TextInput
              style={[styles.input, styles.textArea]}
              placeholder="اكتب وصفاً تفصيلياً للعقار..."
              value={description}
              onChangeText={setDescription}
              multiline
              numberOfLines={4}
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>الفئة *</Text>
            <View style={styles.categoryGrid}>
              {PROPERTY_CATEGORIES.map((cat) => (
                <TouchableOpacity
                  key={cat.value}
                  style={[
                    styles.categoryButton,
                    category === cat.value && styles.categoryButtonActive,
                  ]}
                  onPress={() => setCategory(cat.value)}
                >
                  <Text style={[
                    styles.categoryText,
                    category === cat.value && styles.categoryTextActive,
                  ]}>
                    {cat.label}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>التفاصيل</Text>

          <View style={styles.row}>
            <View style={[styles.inputGroup, styles.flex1]}>
              <Text style={styles.label}>السعر *</Text>
              <TextInput
                style={styles.input}
                placeholder="100000"
                value={price}
                onChangeText={setPrice}
                keyboardType="numeric"
              />
            </View>
            <View style={[styles.inputGroup, styles.flex1]}>
              <Text style={styles.label}>وحدة السعر</Text>
              <View style={styles.unitButtons}>
                {PRICE_UNITS.map((unit) => (
                  <TouchableOpacity
                    key={unit.value}
                    style={[
                      styles.unitButton,
                      priceUnit === unit.value && styles.unitButtonActive,
                    ]}
                    onPress={() => setPriceUnit(unit.value)}
                  >
                    <Text style={[
                      styles.unitText,
                      priceUnit === unit.value && styles.unitTextActive,
                    ]}>
                      {unit.label}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>
          </View>

          <View style={styles.row}>
            <View style={[styles.inputGroup, styles.flex1]}>
              <Text style={styles.label}>المساحة *</Text>
              <TextInput
                style={styles.input}
                placeholder="150"
                value={size}
                onChangeText={setSize}
                keyboardType="numeric"
              />
            </View>
            <View style={[styles.inputGroup, styles.flex1]}>
              <Text style={styles.label}>وحدة المساحة</Text>
              <View style={styles.unitButtons}>
                {SIZE_UNITS.map((unit) => (
                  <TouchableOpacity
                    key={unit.value}
                    style={[
                      styles.unitButton,
                      sizeUnit === unit.value && styles.unitButtonActive,
                    ]}
                    onPress={() => setSizeUnit(unit.value)}
                  >
                    <Text style={[
                      styles.unitText,
                      sizeUnit === unit.value && styles.unitTextActive,
                    ]}>
                      {unit.label}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>الموقع *</Text>
            <TextInput
              style={styles.input}
              placeholder="مثال: تونس العاصمة"
              value={location}
              onChangeText={setLocation}
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>رقم الهاتف *</Text>
            <TextInput
              style={styles.input}
              placeholder="12345678"
              value={phoneNumber}
              onChangeText={setPhoneNumber}
              keyboardType="phone-pad"
            />
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>الصور *</Text>
          
          <TouchableOpacity style={styles.addImageButton} onPress={pickImages}>
            <Text style={styles.addImageText}>📷 إضافة صور جديدة</Text>
          </TouchableOpacity>

          {images.length > 0 && (
            <View style={styles.imagesGrid}>
              {images.map((uri, index) => (
                <View key={index} style={styles.imageContainer}>
                  <Image source={{ uri }} style={styles.image} />
                  <TouchableOpacity
                    style={styles.removeButton}
                    onPress={() => removeImage(index)}
                  >
                    <Text style={styles.removeButtonText}>✕</Text>
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          )}
        </View>

        <Button
          title="حفظ التغييرات"
          onPress={handleUpdate}
          loading={loading}
          style={styles.submitButton}
        />
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  header: {
    padding: Spacing.lg,
    backgroundColor: Colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
  },
  title: {
    fontSize: FontSizes.xxl,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.primary,
    textAlign: 'center',
  },
  scrollView: {
    flex: 1,
  },
  scrollContent: {
    padding: Spacing.md,
  },
  section: {
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
    marginBottom: Spacing.md,
  },
  sectionTitle: {
    fontSize: FontSizes.lg,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.text,
    marginBottom: Spacing.md,
    textAlign: 'right',
  },
  inputGroup: {
    marginBottom: Spacing.md,
  },
  label: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_600SemiBold',
    color: Colors.text,
    marginBottom: Spacing.sm,
    textAlign: 'right',
  },
  input: {
    backgroundColor: Colors.background,
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: BorderRadius.md,
    padding: Spacing.md,
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.text,
    textAlign: 'right',
  },
  textArea: {
    height: 100,
    textAlignVertical: 'top',
  },
  row: {
    flexDirection: 'row',
    gap: Spacing.md,
  },
  flex1: {
    flex: 1,
  },
  categoryGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: Spacing.sm,
  },
  categoryButton: {
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.md,
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.background,
  },
  categoryButtonActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  categoryText: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.text,
  },
  categoryTextActive: {
    color: '#FFF',
  },
  unitButtons: {
    flexDirection: 'row',
    gap: Spacing.xs,
  },
  unitButton: {
    flex: 1,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.md,
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.background,
    alignItems: 'center',
  },
  unitButtonActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  unitText: {
    fontSize: FontSizes.xs,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.text,
  },
  unitTextActive: {
    color: '#FFF',
  },
  addImageButton: {
    backgroundColor: Colors.primaryLight,
    borderWidth: 2,
    borderColor: Colors.primary,
    borderStyle: 'dashed',
    borderRadius: BorderRadius.md,
    padding: Spacing.xl,
    alignItems: 'center',
  },
  addImageText: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_600SemiBold',
    color: Colors.primary,
  },
  imagesGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: Spacing.sm,
    marginTop: Spacing.md,
  },
  imageContainer: {
    width: '31%',
    aspectRatio: 1,
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
    borderRadius: BorderRadius.md,
  },
  removeButton: {
    position: 'absolute',
    top: -8,
    right: -8,
    backgroundColor: Colors.error,
    borderRadius: BorderRadius.full,
    width: 24,
    height: 24,
    alignItems: 'center',
    justifyContent: 'center',
  },
  removeButtonText: {
    color: '#FFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  submitButton: {
    marginTop: Spacing.md,
    marginBottom: Spacing.xl,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: Spacing.md,
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.textSecondary,
  },
});
