import { useState, useEffect } from 'react';
import { api } from '../lib/api';

export function useProperties(filters = {}) {
  const [properties, setProperties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [refreshing, setRefreshing] = useState(false);

  const fetchProperties = async (isRefreshing = false) => {
    try {
      if (isRefreshing) {
        setRefreshing(true);
      } else {
        setLoading(true);
      }
      setError(null);

      const response = await api.getProperties(filters);
      setProperties(response.data || []);
    } catch (err) {
      setError(err.message || 'فشل في تحميل العقارات');
      console.error('Error fetching properties:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchProperties();
  }, [JSON.stringify(filters)]);

  const refresh = () => {
    fetchProperties(true);
  };

  return {
    properties,
    loading,
    error,
    refreshing,
    refresh,
  };
}

export function useProperty(id) {
  const [property, setProperty] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchProperty = async () => {
      try {
        setLoading(true);
        setError(null);
        const data = await api.getProperty(id);
        setProperty(data);
      } catch (err) {
        setError(err.message || 'فشل في تحميل العقار');
        console.error('Error fetching property:', err);
      } finally {
        setLoading(false);
      }
    };

    if (id) {
      fetchProperty();
    }
  }, [id]);

  return { property, loading, error };
}

export function useUserProperties() {
  const [properties, setProperties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [refreshing, setRefreshing] = useState(false);

  const fetchUserProperties = async (isRefreshing = false) => {
    try {
      if (isRefreshing) {
        setRefreshing(true);
      } else {
        setLoading(true);
      }
      setError(null);

      const response = await api.getUserProperties();
      setProperties(response.data || []);
    } catch (err) {
      setError(err.message || 'فشل في تحميل عقاراتك');
      console.error('Error fetching user properties:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchUserProperties();
  }, []);

  const refresh = () => {
    fetchUserProperties(true);
  };

  const deleteProperty = async (id) => {
    try {
      await api.deleteProperty(id);
      setProperties(prev => prev.filter(p => p.id !== id));
      return { success: true };
    } catch (err) {
      return { success: false, error: err.message };
    }
  };

  return {
    properties,
    loading,
    error,
    refreshing,
    refresh,
    deleteProperty,
  };
}
