import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, Platform } from 'react-native';

// Polyfill for web
if (Platform.OS === 'web' && typeof global === 'undefined') {
  window.global = window;
}

export default function App() {
  const [currentScreen, setCurrentScreen] = useState('splash');
  const [isGuest, setIsGuest] = useState(false);

  // Splash Screen
  if (currentScreen === 'splash') {
    setTimeout(() => setCurrentScreen('onboarding'), 2000);
    return (
      <View style={styles.container}>
        <Text style={styles.logo}>🦉</Text>
        <Text style={styles.title}>LinguaLearn</Text>
        <Text style={styles.subtitle}>Learn languages the fun way!</Text>
      </View>
    );
  }

  // Onboarding Screen
  if (currentScreen === 'onboarding') {
    return (
      <View style={styles.container}>
        <Text style={styles.logo}>🦉</Text>
        <Text style={styles.title}>Welcome to LinguaLearn</Text>
        <Text style={styles.description}>
          Learn languages with interactive lessons, earn XP, and track your progress!
        </Text>
        <TouchableOpacity 
          style={styles.button}
          onPress={() => setCurrentScreen('auth')}
        >
          <Text style={styles.buttonText}>Get Started</Text>
        </TouchableOpacity>
      </View>
    );
  }

  // Auth Screen
  if (currentScreen === 'auth') {
    return (
      <View style={styles.container}>
        <Text style={styles.logo}>🦉</Text>
        <Text style={styles.title}>Sign In</Text>
        
        <TextInput
          style={styles.input}
          placeholder="Email"
          placeholderTextColor="#999"
        />
        <TextInput
          style={styles.input}
          placeholder="Password"
          placeholderTextColor="#999"
          secureTextEntry
        />
        
        <TouchableOpacity 
          style={styles.button}
          onPress={() => setCurrentScreen('home')}
        >
          <Text style={styles.buttonText}>Login</Text>
        </TouchableOpacity>

        <TouchableOpacity 
          style={[styles.button, styles.buttonSecondary]}
          onPress={() => {
            setIsGuest(true);
            setCurrentScreen('home');
          }}
        >
          <Text style={styles.buttonText}>Continue as Guest</Text>
        </TouchableOpacity>
      </View>
    );
  }

  // Home Screen
  if (currentScreen === 'home') {
    const courses = [
      { id: 1, name: 'English for Beginners', icon: '🇬🇧', lessons: 5 },
      { id: 2, name: 'Spanish Basics', icon: '🇪🇸', lessons: 3 },
      { id: 3, name: 'French Fundamentals', icon: '🇫🇷', lessons: 2 },
      { id: 4, name: 'German Essentials', icon: '🇩🇪', lessons: 1 },
      { id: 5, name: 'Arabic for Beginners', icon: '🇸🇦', lessons: 1 },
    ];

    return (
      <View style={styles.homeContainer}>
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.headerTitle}>🦉 LinguaLearn</Text>
          <View style={styles.stats}>
            <Text style={styles.statText}>⚡ 450 XP</Text>
            <Text style={styles.statText}>🔥 7 Day Streak</Text>
          </View>
        </View>

        {/* Courses */}
        <ScrollView style={styles.content}>
          <Text style={styles.sectionTitle}>Choose a Course</Text>
          
          {courses.map(course => (
            <TouchableOpacity
              key={course.id}
              style={styles.courseCard}
              onPress={() => setCurrentScreen('lesson')}
            >
              <Text style={styles.courseIcon}>{course.icon}</Text>
              <View style={styles.courseInfo}>
                <Text style={styles.courseName}>{course.name}</Text>
                <Text style={styles.courseLessons}>{course.lessons} lessons</Text>
              </View>
              <Text style={styles.arrow}>›</Text>
            </TouchableOpacity>
          ))}
        </ScrollView>

        {/* Bottom Nav */}
        <View style={styles.bottomNav}>
          <TouchableOpacity style={styles.navItem}>
            <Text style={styles.navIcon}>🏠</Text>
            <Text style={styles.navText}>Home</Text>
          </TouchableOpacity>
          <TouchableOpacity 
            style={styles.navItem}
            onPress={() => setCurrentScreen('profile')}
          >
            <Text style={styles.navIcon}>👤</Text>
            <Text style={styles.navText}>Profile</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  // Lesson Screen
  if (currentScreen === 'lesson') {
    return (
      <View style={styles.homeContainer}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => setCurrentScreen('home')}>
            <Text style={styles.backButton}>← Back</Text>
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Greetings & Introductions</Text>
        </View>

        <ScrollView style={styles.content}>
          <View style={styles.lessonCard}>
            <Text style={styles.lessonIcon}>👋</Text>
            <Text style={styles.lessonTitle}>Lesson 1: Greetings</Text>
            <Text style={styles.lessonDesc}>Learn basic greetings and introductions</Text>
            
            <View style={styles.lessonStats}>
              <Text style={styles.lessonStat}>📝 4 Exercises</Text>
              <Text style={styles.lessonStat}>⚡ 10 XP</Text>
            </View>

            <TouchableOpacity 
              style={styles.button}
              onPress={() => setCurrentScreen('exercise')}
            >
              <Text style={styles.buttonText}>Start Lesson</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </View>
    );
  }

  // Exercise Screen
  if (currentScreen === 'exercise') {
    const [selectedAnswer, setSelectedAnswer] = useState(null);
    const [showResult, setShowResult] = useState(false);

    return (
      <View style={styles.homeContainer}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => setCurrentScreen('lesson')}>
            <Text style={styles.backButton}>✕</Text>
          </TouchableOpacity>
          <View style={styles.progressBar}>
            <View style={styles.progressFill} />
          </View>
        </View>

        <ScrollView style={styles.content}>
          <Text style={styles.question}>How do you say "Hello"?</Text>

          {['Hello', 'Goodbye', 'Thanks', 'Please'].map((option, index) => (
            <TouchableOpacity
              key={index}
              style={[
                styles.optionButton,
                selectedAnswer === index && styles.optionSelected,
                showResult && index === 0 && styles.optionCorrect,
              ]}
              onPress={() => {
                setSelectedAnswer(index);
                setShowResult(true);
              }}
            >
              <Text style={styles.optionText}>{option}</Text>
            </TouchableOpacity>
          ))}

          {showResult && (
            <View style={styles.feedback}>
              <Text style={styles.feedbackText}>
                {selectedAnswer === 0 ? '✅ Correct! +5 XP' : '❌ Try again!'}
              </Text>
              <TouchableOpacity 
                style={styles.button}
                onPress={() => setCurrentScreen('home')}
              >
                <Text style={styles.buttonText}>Continue</Text>
              </TouchableOpacity>
            </View>
          )}
        </ScrollView>
      </View>
    );
  }

  // Profile Screen
  if (currentScreen === 'profile') {
    return (
      <View style={styles.homeContainer}>
        <View style={styles.header}>
          <Text style={styles.headerTitle}>Profile</Text>
        </View>

        <ScrollView style={styles.content}>
          <View style={styles.profileCard}>
            <Text style={styles.profileAvatar}>👤</Text>
            <Text style={styles.profileName}>{isGuest ? 'Guest User' : 'John Doe'}</Text>
            <Text style={styles.profileEmail}>{isGuest ? 'guest@temp.com' : 'john@example.com'}</Text>
          </View>

          <View style={styles.statsGrid}>
            <View style={styles.statCard}>
              <Text style={styles.statNumber}>450</Text>
              <Text style={styles.statLabel}>Total XP</Text>
            </View>
            <View style={styles.statCard}>
              <Text style={styles.statNumber}>7</Text>
              <Text style={styles.statLabel}>Day Streak</Text>
            </View>
          </View>

          <TouchableOpacity 
            style={[styles.button, styles.buttonDanger]}
            onPress={() => setCurrentScreen('auth')}
          >
            <Text style={styles.buttonText}>Logout</Text>
          </TouchableOpacity>
        </ScrollView>

        <View style={styles.bottomNav}>
          <TouchableOpacity 
            style={styles.navItem}
            onPress={() => setCurrentScreen('home')}
          >
            <Text style={styles.navIcon}>🏠</Text>
            <Text style={styles.navText}>Home</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.navItem}>
            <Text style={styles.navIcon}>👤</Text>
            <Text style={styles.navText}>Profile</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  return null;
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#58CC02',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  homeContainer: {
    flex: 1,
    backgroundColor: '#F7F7F7',
  },
  logo: {
    fontSize: 100,
    marginBottom: 20,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginBottom: 10,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 18,
    color: '#FFFFFF',
    textAlign: 'center',
    marginBottom: 40,
  },
  description: {
    fontSize: 16,
    color: '#FFFFFF',
    textAlign: 'center',
    marginBottom: 40,
    paddingHorizontal: 20,
  },
  input: {
    width: '100%',
    maxWidth: 400,
    backgroundColor: '#FFFFFF',
    padding: 15,
    borderRadius: 10,
    marginBottom: 15,
    fontSize: 16,
  },
  button: {
    backgroundColor: '#FFFFFF',
    paddingVertical: 15,
    paddingHorizontal: 40,
    borderRadius: 10,
    marginTop: 10,
    minWidth: 200,
  },
  buttonSecondary: {
    backgroundColor: 'rgba(255,255,255,0.3)',
  },
  buttonDanger: {
    backgroundColor: '#FF4B4B',
  },
  buttonText: {
    color: '#58CC02',
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  header: {
    backgroundColor: '#58CC02',
    padding: 20,
    paddingTop: 40,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#FFFFFF',
    textAlign: 'center',
  },
  stats: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 10,
    gap: 20,
  },
  statText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  content: {
    flex: 1,
    padding: 20,
  },
  sectionTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 20,
  },
  courseCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 15,
    padding: 20,
    marginBottom: 15,
    flexDirection: 'row',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
  },
  courseIcon: {
    fontSize: 40,
    marginRight: 15,
  },
  courseInfo: {
    flex: 1,
  },
  courseName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 5,
  },
  courseLessons: {
    fontSize: 14,
    color: '#777777',
  },
  arrow: {
    fontSize: 30,
    color: '#CCCCCC',
  },
  bottomNav: {
    flexDirection: 'row',
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: '#E5E5E5',
    paddingVertical: 10,
  },
  navItem: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 5,
  },
  navIcon: {
    fontSize: 24,
    marginBottom: 5,
  },
  navText: {
    fontSize: 12,
    color: '#777777',
    fontWeight: '600',
  },
  backButton: {
    fontSize: 18,
    color: '#FFFFFF',
    marginBottom: 10,
  },
  lessonCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 20,
    padding: 30,
    alignItems: 'center',
  },
  lessonIcon: {
    fontSize: 80,
    marginBottom: 20,
  },
  lessonTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 10,
  },
  lessonDesc: {
    fontSize: 16,
    color: '#777777',
    textAlign: 'center',
    marginBottom: 20,
  },
  lessonStats: {
    flexDirection: 'row',
    gap: 20,
    marginBottom: 30,
  },
  lessonStat: {
    fontSize: 14,
    color: '#58CC02',
    fontWeight: '600',
  },
  progressBar: {
    height: 8,
    backgroundColor: 'rgba(255,255,255,0.3)',
    borderRadius: 4,
    marginTop: 10,
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    width: '25%',
    backgroundColor: '#FFFFFF',
  },
  question: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 30,
    textAlign: 'center',
  },
  optionButton: {
    backgroundColor: '#FFFFFF',
    padding: 20,
    borderRadius: 15,
    marginBottom: 15,
    borderWidth: 2,
    borderColor: '#E5E5E5',
  },
  optionSelected: {
    borderColor: '#58CC02',
  },
  optionCorrect: {
    backgroundColor: '#DCFCE7',
    borderColor: '#58CC02',
  },
  optionText: {
    fontSize: 18,
    color: '#3C3C3C',
    textAlign: 'center',
  },
  feedback: {
    backgroundColor: '#FFFFFF',
    padding: 20,
    borderRadius: 15,
    marginTop: 20,
    alignItems: 'center',
  },
  feedbackText: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  profileCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 20,
    padding: 30,
    alignItems: 'center',
    marginBottom: 20,
  },
  profileAvatar: {
    fontSize: 80,
    marginBottom: 15,
  },
  profileName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 5,
  },
  profileEmail: {
    fontSize: 16,
    color: '#777777',
  },
  statsGrid: {
    flexDirection: 'row',
    gap: 15,
    marginBottom: 30,
  },
  statCard: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    borderRadius: 15,
    padding: 20,
    alignItems: 'center',
  },
  statNumber: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#58CC02',
    marginBottom: 5,
  },
  statLabel: {
    fontSize: 14,
    color: '#777777',
  },
});
