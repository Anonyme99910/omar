import { useState, useEffect, useCallback } from 'react';
import { api } from '../lib/api';

export function useFavorites() {
  const [favorites, setFavorites] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [refreshing, setRefreshing] = useState(false);

  const fetchFavorites = async (isRefreshing = false) => {
    try {
      if (isRefreshing) {
        setRefreshing(true);
      } else {
        setLoading(true);
      }
      setError(null);

      const response = await api.getFavorites();
      setFavorites(response.data || []);
    } catch (err) {
      setError(err.message || 'فشل في تحميل المفضلة');
      console.error('Error fetching favorites:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchFavorites();
  }, []);

  const refresh = () => {
    fetchFavorites(true);
  };

  const toggleFavorite = async (propertyId) => {
    try {
      const isFavorited = favorites.some(
        fav => fav.property_id === propertyId || fav.property?.id === propertyId
      );

      if (isFavorited) {
        await api.removeFavorite(propertyId);
        setFavorites(prev => 
          prev.filter(fav => 
            fav.property_id !== propertyId && fav.property?.id !== propertyId
          )
        );
      } else {
        const newFavorite = await api.addFavorite(propertyId);
        setFavorites(prev => [...prev, newFavorite]);
      }

      return { success: true, isFavorited: !isFavorited };
    } catch (err) {
      return { success: false, error: err.message };
    }
  };

  const isFavorited = useCallback((propertyId) => {
    return favorites.some(
      fav => fav.property_id === propertyId || fav.property?.id === propertyId
    );
  }, [favorites]);

  return {
    favorites,
    loading,
    error,
    refreshing,
    refresh,
    toggleFavorite,
    isFavorited,
  };
}
