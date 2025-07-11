// firebase.js
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken,onMessage } from 'firebase/messaging';
import { getAnalytics } from "firebase/analytics";

const firebaseConfig = {
    apiKey: "AIzaSyAB5qjwdvu-2vlvsQxi_fXC6LuAULVOyBY",
    authDomain: "readyhub-5b846.firebaseapp.com",
    projectId: "readyhub-5b846",
    storageBucket: "readyhub-5b846.firebasestorage.app",
    messagingSenderId: "279732323239",
    appId: "1:279732323239:web:e5d86dac89b021c3df2c82",
    measurementId: "G-5C3FJV80CZ"
  };

const app = initializeApp(firebaseConfig);
console.log(app);
const messaging = getMessaging(app);
console.log(messaging.vapidKey);
const analytics = getAnalytics(app);
// Register the service worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then((registration) => {
      console.log('Service Worker registered with scope:', registration.scope);
    }).catch((err) => {
      console.error('Service Worker registration failed:', err);
    });
}

// Listen for messages when the app is in the foreground
onMessage(messaging, (payload) => {
  console.log('Message received. ', payload);
  // Customize notification here
  const notificationTitle = payload.notification.title;
  console.log(notificationTitle);
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/firebase-logo.png' // Optional: Add an icon for the notification
  };
  console.log(notificationOptions);

  if (Notification.permission === 'granted') {
    console.log('Showing notification');
    new Notification(notificationTitle, notificationOptions);
  }
});
export { messaging, getToken };