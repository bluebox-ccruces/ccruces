
import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, Animated } from 'react-native';

export default function LoginScreen({ navigation }) {
  const [userFocus, setUserFocus] = useState(false);
  const [passFocus, setPassFocus] = useState(false);
  const [buttonAnim] = useState(new Animated.Value(1));

  const handlePressIn = () => {
    Animated.spring(buttonAnim, {
      toValue: 0.96,
      useNativeDriver: true,
      speed: 40,
      bounciness: 8,
    }).start();
  };
  const handlePressOut = () => {
    Animated.spring(buttonAnim, {
      toValue: 1,
      useNativeDriver: true,
      speed: 40,
      bounciness: 8,
    }).start();
    navigation.replace('Dashboard');
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>AgroGestor</Text>
      <View style={styles.box}>
        <Text style={styles.label}>Usuario</Text>
        <TextInput
          style={[styles.input, userFocus && styles.inputFocus]}
          placeholder="Ingrese usuario"
          placeholderTextColor="#aaa"
          onFocus={() => setUserFocus(true)}
          onBlur={() => setUserFocus(false)}
        />
        <Text style={styles.label}>Contraseña</Text>
        <TextInput
          style={[styles.input, passFocus && styles.inputFocus]}
          placeholder="Ingrese contraseña"
          placeholderTextColor="#aaa"
          secureTextEntry
          onFocus={() => setPassFocus(true)}
          onBlur={() => setPassFocus(false)}
        />
        <Animated.View style={{ transform: [{ scale: buttonAnim }] }}>
          <TouchableOpacity
            style={styles.button}
            activeOpacity={0.85}
            onPressIn={handlePressIn}
            onPressOut={handlePressOut}
          >
            <Text style={styles.buttonText}>Ingresar</Text>
          </TouchableOpacity>
        </Animated.View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F8FAFB',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingTop: 32,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1A5276',
    marginBottom: 16,
    fontFamily: 'Inter',
    letterSpacing: 0.5,
  },
  box: {
    backgroundColor: 'rgba(255,255,255,0.92)',
    borderRadius: 14,
    paddingVertical: 18,
    paddingHorizontal: 16,
    width: 290,
    shadowColor: '#000',
    shadowOpacity: 0.07,
    shadowRadius: 8,
    elevation: 2,
  },
  label: {
    fontSize: 14,
    color: '#1A5276',
    marginTop: 10,
    fontFamily: 'Inter',
    marginBottom: 2,
  },
  input: {
    borderBottomWidth: 1,
    borderBottomColor: '#1A5276',
    fontSize: 15,
    paddingVertical: 4,
    color: '#222',
    fontFamily: 'Inter',
    backgroundColor: 'rgba(255,255,255,0.7)',
    borderRadius: 5,
    marginBottom: 2,
    transition: 'all 0.2s',
    paddingHorizontal: 6,
  },
  inputFocus: {
    borderBottomColor: '#F39C12',
    backgroundColor: '#FDF6E3',
    shadowColor: '#F39C12',
    shadowOpacity: 0.15,
    shadowRadius: 6,
    elevation: 2,
  },
  button: {
    backgroundColor: '#F39C12',
    borderRadius: 7,
    marginTop: 18,
    paddingVertical: 10,
    alignItems: 'center',
    minHeight: 40,
    minWidth: 120,
  },
  buttonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
    fontFamily: 'Inter',
    letterSpacing: 0.5,
  },
});
