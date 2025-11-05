import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Dimensions,
} from 'react-native';
import api from '../services/api';

const { width } = Dimensions.get('window');

const HomeScreen = ({ navigation }) => {
  const [user, setUser] = useState(null);
  const [courses, setCourses] = useState([]);
  const [selectedCourse, setSelectedCourse] = useState(null);
  const [lessons, setLessons] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      // Get courses (no auth required)
      const coursesResponse = await api.getCourses();
      console.log('Courses Response:', JSON.stringify(coursesResponse, null, 2));
      
      const coursesData = coursesResponse.data || coursesResponse.courses || [];
      console.log('Courses Data:', coursesData.length, 'courses');
      setCourses(coursesData);

      // Try to get user if token exists
      if (global.userToken) {
        try {
          const userResponse = await api.getMe();
          setUser(userResponse.user);
        } catch (error) {
          console.log('User not authenticated');
        }
      }

      if (coursesData && coursesData.length > 0) {
        console.log('First course:', coursesData[0]);
        selectCourse(coursesData[0]);
      }
    } catch (error) {
      console.error('Error fetching data:', error);
      // Set empty courses on error
      setCourses([]);
    } finally {
      setLoading(false);
    }
  };

  const selectCourse = async (course) => {
    setSelectedCourse(course);
    setLessons(course.lessons || []);
  };

  const startLesson = async (lesson) => {
    try {
      await api.startLesson(lesson.id);
      navigation.navigate('Lesson', { lesson });
    } catch (error) {
      console.error('Error starting lesson:', error);
    }
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#58CC02" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerTop}>
          <View>
            <Text style={styles.greeting}>Hello, {user?.name}!</Text>
            <Text style={styles.subtitle}>Keep learning</Text>
          </View>
          <TouchableOpacity
            style={styles.profileButton}
            onPress={() => navigation.navigate('Profile')}
          >
            <Text style={styles.profileEmoji}>👤</Text>
          </TouchableOpacity>
        </View>

        {/* Stats Bar */}
        <View style={styles.statsBar}>
          <View style={styles.statItem}>
            <Text style={styles.statIcon}>🔥</Text>
            <Text style={styles.statValue}>{String(user?.current_streak || 0)}</Text>
            <Text style={styles.statLabel}>Streak</Text>
          </View>
          <View style={styles.statItem}>
            <Text style={styles.statIcon}>⚡</Text>
            <Text style={styles.statValue}>{String(user?.total_xp || 0)}</Text>
            <Text style={styles.statLabel}>Total XP</Text>
          </View>
          <View style={styles.statItem}>
            <Text style={styles.statIcon}>🏆</Text>
            <Text style={styles.statValue}>0</Text>
            <Text style={styles.statLabel}>Rank</Text>
          </View>
        </View>
      </View>

      {/* Course Selector */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.courseSelector}
        contentContainerStyle={styles.courseSelectorContent}
      >
        {courses && courses.length > 0 ? (
          courses.map((course) => (
            <TouchableOpacity
              key={course.id}
              style={[
                styles.courseCard,
                selectedCourse?.id === course.id && styles.courseCardActive,
                { backgroundColor: course.color_primary || '#58CC02' },
              ]}
              onPress={() => selectCourse(course)}
            >
              <Text style={styles.courseFlag}>{course.flag_icon || '🌍'}</Text>
              <Text style={styles.courseName}>{course.name || 'Course'}</Text>
            </TouchableOpacity>
          ))
        ) : (
          <View style={styles.emptyState}>
            <Text style={styles.emptyStateText}>Loading courses...</Text>
          </View>
        )}
      </ScrollView>

      {/* Lesson Path */}
      <ScrollView style={styles.lessonPath} contentContainerStyle={styles.lessonPathContent}>
        <Text style={styles.sectionTitle}>Your Learning Path</Text>

        {lessons && lessons.length > 0 && lessons.map((lesson, index) => (
          <View key={lesson.id} style={styles.lessonContainer}>
            {/* Connecting Line */}
            {index > 0 && <View style={styles.connectingLine} />}

            {/* Lesson Node */}
            <TouchableOpacity
              style={[
                styles.lessonNode,
                !!lesson.is_locked && styles.lessonNodeLocked,
                { backgroundColor: selectedCourse?.color_primary || '#58CC02' },
              ]}
              onPress={() => !lesson.is_locked && startLesson(lesson)}
              disabled={!!lesson.is_locked}
            >
              <Text style={styles.lessonIcon}>{String(lesson.icon || '📚')}</Text>
            </TouchableOpacity>

            {/* Lesson Info */}
            <View style={styles.lessonInfo}>
              <Text style={styles.lessonTitle}>{lesson.title || 'Lesson'}</Text>
              <Text style={styles.lessonDescription}>
                {lesson.description || 'Complete this lesson'}
              </Text>
              <View style={styles.lessonMeta}>
                <Text style={styles.lessonXP}>+{String(lesson.xp_reward || 10)} XP</Text>
                {!!lesson.is_locked && (
                  <Text style={styles.lockedBadge}>🔒 Locked</Text>
                )}
              </View>
            </View>
          </View>
        ))}

        {lessons.length === 0 && (
          <View style={styles.emptyState}>
            <Text style={styles.emptyStateEmoji}>📚</Text>
            <Text style={styles.emptyStateText}>
              No lessons available yet
            </Text>
          </View>
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F7F7F7',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F7F7F7',
  },
  header: {
    backgroundColor: '#FFFFFF',
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: 20,
    borderBottomLeftRadius: 30,
    borderBottomRightRadius: 30,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 5,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  greeting: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3C3C3C',
  },
  subtitle: {
    fontSize: 14,
    color: '#777777',
    marginTop: 2,
  },
  profileButton: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: '#F7F7F7',
    justifyContent: 'center',
    alignItems: 'center',
  },
  profileEmoji: {
    fontSize: 24,
  },
  statsBar: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  statItem: {
    alignItems: 'center',
  },
  statIcon: {
    fontSize: 24,
    marginBottom: 5,
  },
  statValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#3C3C3C',
  },
  statLabel: {
    fontSize: 12,
    color: '#777777',
    marginTop: 2,
  },
  courseSelector: {
    maxHeight: 120,
    marginVertical: 20,
  },
  courseSelectorContent: {
    paddingHorizontal: 20,
  },
  courseCard: {
    width: 100,
    height: 100,
    borderRadius: 15,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
    elevation: 3,
  },
  courseCardActive: {
    borderWidth: 3,
    borderColor: '#FFD700',
  },
  courseFlag: {
    fontSize: 40,
    marginBottom: 5,
  },
  courseName: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#FFFFFF',
    textAlign: 'center',
  },
  lessonPath: {
    flex: 1,
  },
  lessonPathContent: {
    paddingHorizontal: 20,
    paddingBottom: 40,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 20,
  },
  lessonContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 30,
  },
  connectingLine: {
    position: 'absolute',
    left: 35,
    top: -30,
    width: 3,
    height: 30,
    backgroundColor: '#E5E5E5',
  },
  lessonNode: {
    width: 70,
    height: 70,
    borderRadius: 35,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 5,
  },
  lessonNodeLocked: {
    backgroundColor: '#CCCCCC',
  },
  lessonIcon: {
    fontSize: 32,
  },
  lessonInfo: {
    flex: 1,
    marginLeft: 15,
    backgroundColor: '#FFFFFF',
    padding: 15,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  lessonTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 5,
  },
  lessonDescription: {
    fontSize: 13,
    color: '#777777',
    marginBottom: 8,
  },
  lessonMeta: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  lessonXP: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#58CC02',
    marginRight: 10,
  },
  lockedBadge: {
    fontSize: 11,
    color: '#999999',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 60,
  },
  emptyStateEmoji: {
    fontSize: 60,
    marginBottom: 15,
  },
  emptyStateText: {
    fontSize: 16,
    color: '#777777',
  },
});

export default HomeScreen;
