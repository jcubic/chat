/* global importScripts, firebase */

importScripts('https://www.gstatic.com/firebasejs/7.5.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.5.0/firebase-messaging.js');

var firebaseConfig = {
  apiKey: "AIzaSyBX8doVfi1WRWcRV3RPJSIIyaW-8d4Knh8",
  authDomain: "salami-5d4ed.firebaseapp.com",
  projectId: "salami-5d4ed",
  storageBucket: "salami-5d4ed.appspot.com",
  messagingSenderId: "155859060262",
  appId: "1:155859060262:web:d40ae3a62f09f9d04048e7",
  measurementId: "G-LBJJQX6YPV"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  const {title, ...options} = payload.notification;
  return self.registration.showNotification(title, options);
});

self.addEventListener('install', self.skipWaiting);
self.addEventListener('activate', self.skipWaiting);
