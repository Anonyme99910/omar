import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  StyleSheet,
  ScrollView,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { router } from 'expo-router';
import { Button } from '../../components/Button';
import { useAuthContext } from '../../context/auth-context';
import { api } from '../../lib/api';
import { Colors, Spacing, BorderRadius, FontSizes } from '../../constants/theme';

export default function EditProfileScreen() {
  const { profile, refreshProfile } = useAuthContext();
  const [fullName, setFullName] = useState(profile?.full_name || '');
  const [phoneNumber, setPhoneNumber] = useState(profile?.phone_number || '');
  const [loading, setLoading] = useState(false);

  const handleUpdate = async () => {
    if (!fullName || !phoneNumber) {
      Alert.alert('خطأ', 'الرجاء ملء جميع الحقول');
      return;
    }

    try {
      setLoading(true);
      await api.updateProfile({
        full_name: fullName,
        phone_number: phoneNumber,
      });
      await refreshProfile();
      Alert.alert('نجح', 'تم تحديث الملف الشخصي بنجاح', [
        { text: 'حسناً', onPress: () => router.back() }
      ]);
    } catch (error) {
      Alert.alert('خطأ', error.message || 'فشل في تحديث الملف الشخصي');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>✏️ تعديل الملف الشخصي</Text>
      </View>

      <ScrollView style={styles.scrollView} contentContainerStyle={styles.scrollContent}>
        <View style={styles.form}>
          <View style={styles.inputGroup}>
            <Text style={styles.label}>الاسم الكامل</Text>
            <TextInput
              style={styles.input}
              placeholder="أدخل اسمك الكامل"
              value={fullName}
              onChangeText={setFullName}
              autoCapitalize="words"
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>رقم الهاتف</Text>
            <TextInput
              style={styles.input}
              placeholder="12345678"
              value={phoneNumber}
              onChangeText={setPhoneNumber}
              keyboardType="phone-pad"
            />
          </View>

          <Button
            title="حفظ التغييرات"
            onPress={handleUpdate}
            loading={loading}
            style={styles.submitButton}
          />

          <Button
            title="إلغاء"
            onPress={() => router.back()}
            variant="outline"
          />
        </View>
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
    padding: Spacing.lg,
  },
  form: {
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
  },
  inputGroup: {
    marginBottom: Spacing.lg,
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
  submitButton: {
    marginBottom: Spacing.md,
  },
});
