import { useEffect, useState } from 'react';
import { Redirect } from 'expo-router';
import { useAuth } from '../context/auth-context';
import { View, Text, StyleSheet } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

export default function Index() {
  const { user, isLoading } = useAuth();
  const [showSplash, setShowSplash] = useState(true);

  useEffect(() => {
    // Show splash for 2 seconds
    const timer = setTimeout(() => {
      setShowSplash(false);
    }, 2000);

    return () => clearTimeout(timer);
  }, []);

  // Show splash screen
  if (showSplash) {
    return (
      <LinearGradient
        colors={['#FF385C', '#E61E4D', '#C13584']}
        style={styles.splashContainer}
      >
        <View style={styles.splashContent}>
          <Text style={styles.logoIcon}>🏠</Text>
          <Text style={styles.logoText}>Parfumes</Text>
          <Text style={styles.tagline}>اكتشف منزل أحلامك</Text>
        </View>
      </LinearGradient>
    );
  }

  // Show loading while checking auth
  if (isLoading) {
    return (
      <LinearGradient
        colors={['#FF385C', '#E61E4D', '#C13584']}
        style={styles.splashContainer}
      >
        <View style={styles.splashContent}>
          <Text style={styles.logoIcon}>🏠</Text>
          <Text style={styles.logoText}>Parfumes</Text>
        </View>
      </LinearGradient>
    );
  }

  // If user is logged in, go to tabs (home)
  // If not logged in, go to auth (login)
  return <Redirect href={user ? '/(tabs)' : '/auth/login'} />;
}

const styles = StyleSheet.create({
  splashContainer: {
    flex: 1,
  },
  splashContent: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  logoIcon: {
    fontSize: 80,
    marginBottom: 10,
  },
  logoText: {
    fontSize: 48,
    fontWeight: 'bold',
    color: '#FFFFFF',
    letterSpacing: 2,
  },
  tagline: {
    fontSize: 18,
    color: '#FFFFFF',
    opacity: 0.9,
    fontWeight: '300',
    marginTop: 10,
  },
});
