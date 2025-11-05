import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  TextInput,
  ScrollView,
  ActivityIndicator,
  Animated,
} from 'react-native';
import api from '../services/api';

const ExerciseScreen = ({ route, navigation }) => {
  const { lesson, exercises: initialExercises } = route.params;
  const [exercises, setExercises] = useState([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [userAnswer, setUserAnswer] = useState('');
  const [selectedOption, setSelectedOption] = useState(null);
  const [showResult, setShowResult] = useState(false);
  const [isCorrect, setIsCorrect] = useState(false);
  const [loading, setLoading] = useState(true);
  const [totalXP, setTotalXP] = useState(0);
  const progressAnim = new Animated.Value(0);

  useEffect(() => {
    fetchExercises();
  }, []);

  useEffect(() => {
    animateProgress();
  }, [currentIndex]);

  const fetchExercises = async () => {
    try {
      if (initialExercises && initialExercises.length > 0) {
        setExercises(initialExercises);
      } else {
        const response = await api.getLessonExercises(lesson.id);
        setExercises(response.exercises || []);
      }
    } catch (error) {
      console.error('Error fetching exercises:', error);
    } finally {
      setLoading(false);
    }
  };

  const animateProgress = () => {
    const progress = exercises.length > 0 ? (currentIndex + 1) / exercises.length : 0;
    Animated.timing(progressAnim, {
      toValue: progress,
      duration: 300,
      useNativeDriver: false,
    }).start();
  };

  const submitAnswer = async () => {
    const exercise = exercises[currentIndex];
    const answer = exercise.type === 'multiple_choice' ? selectedOption : userAnswer;

    if (!answer) {
      alert('Please provide an answer');
      return;
    }

    try {
      const response = await api.submitAnswer(exercise.id, answer);
      setIsCorrect(response.is_correct);
      setShowResult(true);

      if (response.is_correct) {
        setTotalXP(totalXP + response.xp_earned);
      }
    } catch (error) {
      console.error('Error submitting answer:', error);
    }
  };

  const nextExercise = () => {
    if (currentIndex < exercises.length - 1) {
      setCurrentIndex(currentIndex + 1);
      setUserAnswer('');
      setSelectedOption(null);
      setShowResult(false);
    } else {
      completeLesson();
    }
  };

  const completeLesson = async () => {
    try {
      await api.completeLesson(lesson.id);
      // Navigate back to home with success message
      navigation.navigate('Main', {
        screen: 'Home',
        params: { lessonCompleted: true, xp: totalXP }
      });
    } catch (error) {
      console.error('Error completing lesson:', error);
      navigation.goBack();
    }
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#58CC02" />
      </View>
    );
  }

  if (exercises.length === 0) {
    return (
      <View style={styles.emptyContainer}>
        <Text style={styles.emptyText}>No exercises available</Text>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const exercise = exercises[currentIndex];
  const progressWidth = progressAnim.interpolate({
    inputRange: [0, 1],
    outputRange: ['0%', '100%'],
  });

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.closeButton}>✕</Text>
        </TouchableOpacity>
        <View style={styles.progressBarContainer}>
          <Animated.View
            style={[styles.progressBar, { width: progressWidth }]}
          />
        </View>
        <Text style={styles.progressText}>
          {currentIndex + 1}/{exercises.length}
        </Text>
      </View>

      <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
        {/* Question */}
        <Text style={styles.questionType}>{exercise.type.replace('_', ' ').toUpperCase()}</Text>
        <Text style={styles.question}>{exercise.question}</Text>

        {/* Exercise Type Rendering */}
        {exercise.type === 'multiple_choice' && (
          <View style={styles.optionsContainer}>
            {exercise.options?.map((option, index) => (
              <TouchableOpacity
                key={index}
                style={[
                  styles.option,
                  selectedOption === option && styles.optionSelected,
                  showResult && option === exercise.correct_answer && styles.optionCorrect,
                  showResult && selectedOption === option && !isCorrect && styles.optionWrong,
                ]}
                onPress={() => !showResult && setSelectedOption(option)}
                disabled={!!showResult}
              >
                <Text style={styles.optionText}>{option}</Text>
              </TouchableOpacity>
            ))}
          </View>
        )}

        {(exercise.type === 'translate' || exercise.type === 'fill_blank') && (
          <TextInput
            style={[
              styles.input,
              showResult && isCorrect && styles.inputCorrect,
              showResult && !isCorrect && styles.inputWrong,
            ]}
            placeholder="Type your answer..."
            value={userAnswer}
            onChangeText={setUserAnswer}
            editable={!showResult}
            autoFocus
          />
        )}

        {exercise.type === 'listen' && (
          <View style={styles.audioContainer}>
            <TouchableOpacity style={styles.audioButton}>
              <Text style={styles.audioIcon}>🔊</Text>
              <Text style={styles.audioText}>Play Audio</Text>
            </TouchableOpacity>
            <TextInput
              style={styles.input}
              placeholder="Type what you hear..."
              value={userAnswer}
              onChangeText={setUserAnswer}
              editable={!showResult}
            />
          </View>
        )}

        {exercise.type === 'speak' && (
          <View style={styles.speakContainer}>
            <TouchableOpacity style={styles.micButton}>
              <Text style={styles.micIcon}>🎤</Text>
            </TouchableOpacity>
            <Text style={styles.speakText}>Tap to speak</Text>
          </View>
        )}

        {/* Result Feedback */}
        {showResult && (
          <View style={[
            styles.resultContainer,
            isCorrect ? styles.resultCorrect : styles.resultWrong
          ]}>
            <Text style={styles.resultEmoji}>{isCorrect ? '✅' : '❌'}</Text>
            <Text style={styles.resultText}>
              {isCorrect ? 'Correct!' : 'Incorrect'}
            </Text>
            {!isCorrect && (
              <Text style={styles.correctAnswer}>
                Correct answer: {exercise.correct_answer}
              </Text>
            )}
            {exercise.explanation && (
              <Text style={styles.explanation}>{exercise.explanation}</Text>
            )}
            {isCorrect && (
              <Text style={styles.xpEarned}>+{exercise.xp_reward} XP</Text>
            )}
          </View>
        )}
      </ScrollView>

      {/* Action Button */}
      <View style={styles.footer}>
        {!showResult ? (
          <TouchableOpacity style={styles.checkButton} onPress={submitAnswer}>
            <Text style={styles.checkButtonText}>CHECK</Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity
            style={[styles.checkButton, isCorrect && styles.continueButton]}
            onPress={nextExercise}
          >
            <Text style={styles.checkButtonText}>
              {currentIndex < exercises.length - 1 ? 'CONTINUE' : 'FINISH'}
            </Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  emptyText: {
    fontSize: 18,
    color: '#777777',
    marginBottom: 20,
  },
  backButton: {
    backgroundColor: '#58CC02',
    paddingHorizontal: 30,
    paddingVertical: 15,
    borderRadius: 12,
  },
  backButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 50,
    paddingBottom: 20,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E5E5',
  },
  closeButton: {
    fontSize: 24,
    color: '#777777',
    marginRight: 15,
  },
  progressBarContainer: {
    flex: 1,
    height: 10,
    backgroundColor: '#E5E5E5',
    borderRadius: 5,
    overflow: 'hidden',
  },
  progressBar: {
    height: '100%',
    backgroundColor: '#58CC02',
  },
  progressText: {
    marginLeft: 15,
    fontSize: 14,
    fontWeight: 'bold',
    color: '#777777',
  },
  content: {
    flex: 1,
  },
  contentContainer: {
    padding: 20,
  },
  questionType: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#58CC02',
    marginBottom: 10,
  },
  question: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3C3C3C',
    marginBottom: 30,
  },
  optionsContainer: {
    marginTop: 20,
  },
  option: {
    backgroundColor: '#F7F7F7',
    padding: 20,
    borderRadius: 12,
    marginBottom: 15,
    borderWidth: 2,
    borderColor: '#E5E5E5',
  },
  optionSelected: {
    borderColor: '#58CC02',
    backgroundColor: '#F0FDF4',
  },
  optionCorrect: {
    borderColor: '#58CC02',
    backgroundColor: '#DCFCE7',
  },
  optionWrong: {
    borderColor: '#FF4B4B',
    backgroundColor: '#FEE2E2',
  },
  optionText: {
    fontSize: 18,
    color: '#3C3C3C',
  },
  input: {
    backgroundColor: '#F7F7F7',
    padding: 20,
    borderRadius: 12,
    fontSize: 18,
    borderWidth: 2,
    borderColor: '#E5E5E5',
    marginTop: 20,
  },
  inputCorrect: {
    borderColor: '#58CC02',
    backgroundColor: '#F0FDF4',
  },
  inputWrong: {
    borderColor: '#FF4B4B',
    backgroundColor: '#FEE2E2',
  },
  audioContainer: {
    marginTop: 20,
  },
  audioButton: {
    backgroundColor: '#58CC02',
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: 20,
  },
  audioIcon: {
    fontSize: 40,
    marginBottom: 10,
  },
  audioText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  speakContainer: {
    alignItems: 'center',
    marginTop: 40,
  },
  micButton: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: '#58CC02',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  micIcon: {
    fontSize: 60,
  },
  speakText: {
    fontSize: 18,
    color: '#777777',
  },
  resultContainer: {
    marginTop: 30,
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
  },
  resultCorrect: {
    backgroundColor: '#DCFCE7',
  },
  resultWrong: {
    backgroundColor: '#FEE2E2',
  },
  resultEmoji: {
    fontSize: 50,
    marginBottom: 10,
  },
  resultText: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  correctAnswer: {
    fontSize: 16,
    color: '#777777',
    marginBottom: 10,
  },
  explanation: {
    fontSize: 14,
    color: '#777777',
    textAlign: 'center',
    marginTop: 10,
  },
  xpEarned: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#58CC02',
    marginTop: 15,
  },
  footer: {
    padding: 20,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: '#E5E5E5',
  },
  checkButton: {
    backgroundColor: '#E5E5E5',
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
  },
  continueButton: {
    backgroundColor: '#58CC02',
  },
  checkButtonText: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
});

export default ExerciseScreen;
