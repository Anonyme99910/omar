import { AppRegistry } from 'react-native';
import App from './App.web';
import { name as appName } from './app.json';

// Polyfills for web
if (typeof global === 'undefined') {
  window.global = window;
}
if (typeof process === 'undefined') {
  window.process = { env: { NODE_ENV: 'development' } };
}
if (typeof Buffer === 'undefined') {
  window.Buffer = { isBuffer: () => false };
}

AppRegistry.registerComponent(appName, () => App);
AppRegistry.runApplication(appName, {
  rootTag: document.getElementById('root'),
});
