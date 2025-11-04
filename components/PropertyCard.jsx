import React from 'react';
import { View, Text, Image, TouchableOpacity, StyleSheet } from 'react-native';
import { Heart } from 'lucide-react-native';
import { Colors, Spacing, BorderRadius, FontSizes, Shadows } from '../constants/theme';
import { router } from 'expo-router';

export function PropertyCard({ property, onFavoritePress, isFavorited = false }) {
  const handlePress = () => {
    router.push(`/property/${property.id}`);
  };

  const formatPrice = (price, unit) => {
    return `${price.toLocaleString('ar-TN')} ${unit === 'tnd' ? 'دينار' : unit}`;
  };

  const imageUrl = property.images && property.images.length > 0
    ? property.images[0]
    : 'https://via.placeholder.com/400x300?text=No+Image';

  return (
    <TouchableOpacity style={styles.card} onPress={handlePress} activeOpacity={0.9}>
      <View style={styles.imageContainer}>
        <Image source={{ uri: imageUrl }} style={styles.image} resizeMode="cover" />
        
        {onFavoritePress && (
          <TouchableOpacity
            style={styles.favoriteButton}
            onPress={() => onFavoritePress(property.id)}
          >
            <Heart
              size={24}
              color={isFavorited ? Colors.error : '#FFF'}
              fill={isFavorited ? Colors.error : 'transparent'}
            />
          </TouchableOpacity>
        )}

        <View style={styles.categoryBadge}>
          <Text style={styles.categoryText}>{property.category}</Text>
        </View>
      </View>

      <View style={styles.content}>
        <Text style={styles.title} numberOfLines={2}>
          {property.title}
        </Text>
        
        <Text style={styles.location} numberOfLines={1}>
          📍 {property.location}
        </Text>

        <View style={styles.details}>
          <Text style={styles.size}>
            {property.size} {property.size_unit}
          </Text>
          <Text style={styles.price}>
            {formatPrice(property.price, property.price_unit)}
          </Text>
        </View>

        {property.owner && (
          <Text style={styles.owner} numberOfLines={1}>
            {property.owner.full_name}
          </Text>
        )}
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    marginBottom: Spacing.md,
    overflow: 'hidden',
    ...Shadows.medium,
  },
  imageContainer: {
    width: '100%',
    height: 200,
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  favoriteButton: {
    position: 'absolute',
    top: Spacing.md,
    right: Spacing.md,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    borderRadius: BorderRadius.full,
    padding: Spacing.sm,
  },
  categoryBadge: {
    position: 'absolute',
    bottom: Spacing.md,
    left: Spacing.md,
    backgroundColor: Colors.primary,
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.md,
  },
  categoryText: {
    color: '#FFF',
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_600SemiBold',
  },
  content: {
    padding: Spacing.md,
  },
  title: {
    fontSize: FontSizes.lg,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.text,
    marginBottom: Spacing.sm,
  },
  location: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
    marginBottom: Spacing.md,
  },
  details: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: Spacing.sm,
  },
  size: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.textSecondary,
  },
  price: {
    fontSize: FontSizes.lg,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.primary,
  },
  owner: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
  },
});
