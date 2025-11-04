import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  Image,
  StyleSheet,
  TouchableOpacity,
  Linking,
  Alert,
  Dimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { router, useLocalSearchParams } from 'expo-router';
import { ArrowLeft, Heart, Phone, MapPin, Maximize } from 'lucide-react-native';
import { useProperty } from '../../hooks/useProperties';
import { useFavorites } from '../../hooks/useFavorites';
import { Button } from '../../components/Button';
import { Colors, Spacing, BorderRadius, FontSizes, Shadows } from '../../constants/theme';

const { width } = Dimensions.get('window');

export default function PropertyDetailsScreen() {
  const { id } = useLocalSearchParams();
  const { property, loading } = useProperty(id);
  const { toggleFavorite, isFavorited } = useFavorites();
  const [activeImage, setActiveImage] = useState(0);

  const handleCall = () => {
    if (property?.phone_number) {
      Linking.openURL(`tel:${property.phone_number}`);
    }
  };

  const handleFavorite = async () => {
    const result = await toggleFavorite(id);
    if (result.success) {
      Alert.alert(
        'نجح',
        result.isFavorited ? 'تمت الإضافة إلى المفضلة' : 'تمت الإزالة من المفضلة'
      );
    }
  };

  if (loading || !property) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <Text style={styles.loadingText}>جاري التحميل...</Text>
        </View>
      </SafeAreaView>
    );
  }

  const formatPrice = (price, unit) => {
    return `${price.toLocaleString('ar-TN')} ${unit === 'tnd' ? 'دينار' : unit}`;
  };

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView style={styles.scrollView}>
        {/* Image Gallery */}
        <View style={styles.imageSection}>
          <ScrollView
            horizontal
            pagingEnabled
            showsHorizontalScrollIndicator={false}
            onScroll={(e) => {
              const index = Math.round(e.nativeEvent.contentOffset.x / width);
              setActiveImage(index);
            }}
            scrollEventThrottle={16}
          >
            {property.images && property.images.length > 0 ? (
              property.images.map((uri, index) => (
                <Image
                  key={index}
                  source={{ uri }}
                  style={styles.image}
                  resizeMode="cover"
                />
              ))
            ) : (
              <Image
                source={{ uri: 'https://via.placeholder.com/400x300?text=No+Image' }}
                style={styles.image}
                resizeMode="cover"
              />
            )}
          </ScrollView>

          {/* Image Indicators */}
          {property.images && property.images.length > 1 && (
            <View style={styles.indicators}>
              {property.images.map((_, index) => (
                <View
                  key={index}
                  style={[
                    styles.indicator,
                    activeImage === index && styles.indicatorActive,
                  ]}
                />
              ))}
            </View>
          )}

          {/* Back Button */}
          <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <ArrowLeft size={24} color="#FFF" />
          </TouchableOpacity>

          {/* Favorite Button */}
          <TouchableOpacity style={styles.favoriteButton} onPress={handleFavorite}>
            <Heart
              size={24}
              color={isFavorited(id) ? Colors.error : '#FFF'}
              fill={isFavorited(id) ? Colors.error : 'transparent'}
            />
          </TouchableOpacity>
        </View>

        {/* Content */}
        <View style={styles.content}>
          {/* Title & Category */}
          <View style={styles.titleSection}>
            <Text style={styles.title}>{property.title}</Text>
            <View style={styles.categoryBadge}>
              <Text style={styles.categoryText}>{property.category}</Text>
            </View>
          </View>

          {/* Price */}
          <Text style={styles.price}>{formatPrice(property.price, property.price_unit)}</Text>

          {/* Location */}
          <View style={styles.locationRow}>
            <MapPin size={20} color={Colors.primary} />
            <Text style={styles.location}>{property.location}</Text>
          </View>

          {/* Details Grid */}
          <View style={styles.detailsGrid}>
            <View style={styles.detailCard}>
              <Maximize size={24} color={Colors.primary} />
              <Text style={styles.detailLabel}>المساحة</Text>
              <Text style={styles.detailValue}>
                {property.size} {property.size_unit}
              </Text>
            </View>
            <View style={styles.detailCard}>
              <Phone size={24} color={Colors.primary} />
              <Text style={styles.detailLabel}>الهاتف</Text>
              <Text style={styles.detailValue}>{property.phone_number}</Text>
            </View>
          </View>

          {/* Description */}
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>الوصف</Text>
            <Text style={styles.description}>{property.description}</Text>
          </View>

          {/* Owner Info */}
          {property.owner && (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>المالك</Text>
              <View style={styles.ownerCard}>
                <View style={styles.ownerAvatar}>
                  <Text style={styles.ownerInitial}>
                    {property.owner.full_name.charAt(0)}
                  </Text>
                </View>
                <View style={styles.ownerInfo}>
                  <Text style={styles.ownerName}>{property.owner.full_name}</Text>
                  <Text style={styles.ownerPhone}>{property.owner.phone_number}</Text>
                </View>
              </View>
            </View>
          )}

          {/* Call Button */}
          <Button
            title="📞 اتصل الآن"
            onPress={handleCall}
            style={styles.callButton}
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
  scrollView: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.textSecondary,
  },
  imageSection: {
    height: 300,
    position: 'relative',
  },
  image: {
    width,
    height: 300,
  },
  indicators: {
    position: 'absolute',
    bottom: Spacing.md,
    left: 0,
    right: 0,
    flexDirection: 'row',
    justifyContent: 'center',
    gap: Spacing.xs,
  },
  indicator: {
    width: 8,
    height: 8,
    borderRadius: BorderRadius.full,
    backgroundColor: 'rgba(255, 255, 255, 0.5)',
  },
  indicatorActive: {
    backgroundColor: '#FFF',
    width: 24,
  },
  backButton: {
    position: 'absolute',
    top: Spacing.md,
    left: Spacing.md,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    borderRadius: BorderRadius.full,
    padding: Spacing.sm,
  },
  favoriteButton: {
    position: 'absolute',
    top: Spacing.md,
    right: Spacing.md,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    borderRadius: BorderRadius.full,
    padding: Spacing.sm,
  },
  content: {
    padding: Spacing.lg,
  },
  titleSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: Spacing.md,
  },
  title: {
    flex: 1,
    fontSize: FontSizes.xxl,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.text,
  },
  categoryBadge: {
    backgroundColor: Colors.primary,
    paddingHorizontal: Spacing.md,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.md,
    marginLeft: Spacing.sm,
  },
  categoryText: {
    color: '#FFF',
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_600SemiBold',
  },
  price: {
    fontSize: FontSizes.xxxl,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.primary,
    marginBottom: Spacing.md,
  },
  locationRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: Spacing.lg,
  },
  location: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_500Medium',
    color: Colors.textSecondary,
    marginLeft: Spacing.sm,
  },
  detailsGrid: {
    flexDirection: 'row',
    gap: Spacing.md,
    marginBottom: Spacing.lg,
  },
  detailCard: {
    flex: 1,
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
    alignItems: 'center',
    ...Shadows.small,
  },
  detailLabel: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
    marginTop: Spacing.sm,
  },
  detailValue: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.text,
    marginTop: Spacing.xs,
  },
  section: {
    marginBottom: Spacing.lg,
  },
  sectionTitle: {
    fontSize: FontSizes.lg,
    fontFamily: 'Tajawal_700Bold',
    color: Colors.text,
    marginBottom: Spacing.md,
    textAlign: 'right',
  },
  description: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
    lineHeight: 24,
    textAlign: 'right',
  },
  ownerCard: {
    flexDirection: 'row',
    backgroundColor: Colors.surface,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
    alignItems: 'center',
    ...Shadows.small,
  },
  ownerAvatar: {
    width: 50,
    height: 50,
    borderRadius: BorderRadius.full,
    backgroundColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: Spacing.md,
  },
  ownerInitial: {
    fontSize: FontSizes.xl,
    fontFamily: 'Tajawal_700Bold',
    color: '#FFF',
  },
  ownerInfo: {
    flex: 1,
  },
  ownerName: {
    fontSize: FontSizes.md,
    fontFamily: 'Tajawal_600SemiBold',
    color: Colors.text,
    marginBottom: Spacing.xs,
    textAlign: 'right',
  },
  ownerPhone: {
    fontSize: FontSizes.sm,
    fontFamily: 'Tajawal_400Regular',
    color: Colors.textSecondary,
    textAlign: 'right',
  },
  callButton: {
    marginTop: Spacing.md,
    marginBottom: Spacing.xl,
  },
});
