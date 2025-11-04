import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { router } from 'expo-router';
import { User, Building, Heart, Settings, LogOut, Edit } from 'lucide-react-native';
import { useAuthContext } from '../../context/auth-context';
import { Colors, Spacing, BorderRadius, FontSizes, Shadows } from '../../constants/theme';

export default function ProfileScreen() {
  const { user, profile, logout } = useAuthContext();

  const handleLogout = () => {
    Alert.alert(
      'تسجيل الخروج',
      'هل أنت متأكد من تسجيل الخروج؟',
      [
        { text: 'إلغاء', style: 'cancel' },
        {
          text: 'تسجيل الخروج',
          style: 'destructive',
          onPress: async () => {
            await logout();
            router.replace('/auth/login');
          },
        },
      ]
    );
  };

  const menuItems = [
    {
      icon: Building,
      title: 'عقاراتي',
      subtitle: 'إدارة العقارات الخاصة بك',
      onPress: () => router.push('/profile/my-properties'),
    },
    {
      icon: Edit,
      title: 'تعديل الملف الشخصي',
      subtitle: 'تحديث معلوماتك الشخصية',
      onPress: () => router.push('/profile/edit-profile'),
    },
    {
      icon: Settings,
      title: 'تغيير كلمة المرور',
      subtitle: 'تحديث كلمة المرور',
      onPress: () => router.push('/profile/change-password'),
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>👤 الحساب</Text>
      </View>

      <ScrollView style={styles.scrollView} contentContainerStyle={styles.scrollContent}>
        <View style={styles.profileCard}>
          <View style={styles.avatar}>
            <User size={48} color={Colors.primary} />
          </View>
          <Text style={styles.name}>{profile?.full_name || 'المستخدم'}</Text>
          <Text style={styles.email}>{user?.email}</Text>
          <Text style={styles.phone}>📱 {profile?.phone_number}</Text>
        </View>

        <View style={styles.menuSection}>
          {menuItems.map((item, index) => (
            <TouchableOpacity
              key={index}
              style={styles.menuItem}
              onPress={item.onPress}
              activeOpacity={0.7}
            >
              <View style={styles.menuIcon}>
                <item.icon size={24} color={Colors.primary} />
              </View>
              <View style={styles.menuContent}>
                <Text style={styles.menuTitle}>{item.title}</Text>
                <Text style={styles.menuSubtitle}>{item.subtitle}</Text>
              </View>
              <Text style={styles.menuArrow}>‹</Text>
            </TouchableOpacity>
          ))}
        </View>

        <TouchableOpacity
          style={styles.logoutButton}
          onPress={handleLogout}
          activeOpacity={0.7}
        >
          <LogOut size={20} color={Colors.error} />
          <Text style={styles.logoutText}>تسجيل الخروج</Text>
        </TouchableOpacity>

        <View style={styles.footer}>
          <Text style={styles.footerText}>🌸 Parfumes</Text>
          <Text style={styles.footerVersion}>الإصدار 1.0.0</Text>
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
    padding: Spacing.md,
  },
  profileCard: {
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    padding: Spacing.xl,
    alignItems: 'center',
    marginBottom: Spacing.md,
    ...Shadows.medium,
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: BorderRadius.full,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: Spacing.md,
  },
  name: {
    fontSize: FontSizes.xl,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.text,
    marginBottom: Spacing.xs,
  },
  email: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
    marginBottom: Spacing.xs,
  },
  phone: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.textSecondary,
  },
  menuSection: {
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    overflow: 'hidden',
    marginBottom: Spacing.md,
    ...Shadows.small,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: Spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
  },
  menuIcon: {
    width: 40,
    height: 40,
    borderRadius: BorderRadius.md,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: Spacing.md,
  },
  menuContent: {
    flex: 1,
  },
  menuTitle: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_600SemiBold',
    color: Colors.text,
    marginBottom: Spacing.xs,
    textAlign: 'right',
  },
  menuSubtitle: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
    textAlign: 'right',
  },
  menuArrow: {
    fontSize: 24,
    color: Colors.textSecondary,
    marginLeft: Spacing.sm,
  },
  logoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
    borderWidth: 1,
    borderColor: Colors.error,
    ...Shadows.small,
  },
  logoutText: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_600SemiBold',
    color: Colors.error,
    marginLeft: Spacing.sm,
  },
  footer: {
    alignItems: 'center',
    marginTop: Spacing.xl,
    marginBottom: Spacing.lg,
  },
  footerText: {
    fontSize: FontSizes.lg,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.primary,
    marginBottom: Spacing.xs,
  },
  footerVersion: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
  },
});
