import { useEffect, useState } from 'react';
import * as Font from 'expo-font';
import { Inter_400Regular, Inter_500Medium, Inter_600SemiBold, Inter_700Bold } from '@expo-google-fonts/inter';
import { Tajawal_400Regular, Tajawal_500Medium, Tajawal_700Bold } from '@expo-google-fonts/tajawal';

export function useFrameworkReady() {
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    async function loadResources() {
      try {
        await Font.loadAsync({
          Inter_400Regular,
          Inter_500Medium,
          Inter_600SemiBold,
          Inter_700Bold,
          Tajawal_400Regular,
          Tajawal_500Medium,
          Tajawal_700Bold,
        });
      } catch (error) {
        console.error('Error loading fonts:', error);
      } finally {
        setIsReady(true);
      }
    }

    loadResources();
  }, []);

  return isReady;
}
