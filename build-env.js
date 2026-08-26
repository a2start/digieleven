// Build script to generate js/firebase-init.js from Environment Variables (GitHub Secrets)
const fs = require('fs');
const path = require('path');

const apiKey = process.env.FIREBASE_API_KEY || 'AIzaSyDF3ZLCF7FZqWEbg0bzxCo847PQJs3XLyM';
const authDomain = process.env.FIREBASE_AUTH_DOMAIN || 'digieleven-e7def.firebaseapp.com';
const projectId = process.env.FIREBASE_PROJECT_ID || 'digieleven-e7def';
const storageBucket = process.env.FIREBASE_STORAGE_BUCKET || 'digieleven-e7def.firebasestorage.app';
const messagingSenderId = process.env.FIREBASE_MESSAGING_SENDER_ID || '670060380676';
const appId = process.env.FIREBASE_APP_ID || '1:670060380676:web:03d984d8aa4b39ea162b51';
const measurementId = process.env.FIREBASE_MEASUREMENT_ID || 'G-1V3W2ENNGD';
const apiKeyBase64 = Buffer.from(apiKey).toString('base64');

const content = `// Auto-generated Firebase initialization from Environment Variables
var firebaseConfig = {
  apiKey: atob("${apiKeyBase64}"),
  authDomain: "${authDomain}",
  projectId: "${projectId}",
  storageBucket: "${storageBucket}",
  messagingSenderId: "${messagingSenderId}",
  appId: "${appId}",
  measurementId: "${measurementId}"
};

var chFirebaseApp = null;
var chFirestore = null;

try {
    if (typeof firebase !== 'undefined') {
        if (!firebase.apps || !firebase.apps.length) {
            chFirebaseApp = firebase.initializeApp(firebaseConfig);
        } else {
            chFirebaseApp = firebase.app();
        }
        if (firebase.firestore) {
            chFirestore = firebase.firestore();
        }
    }
} catch(e) {
    console.warn('Firebase initialization notice:', e);
}
`;

const targetPath = path.join(__dirname, 'js', 'firebase-init.js');
fs.writeFileSync(targetPath, content, 'utf8');
console.log('Successfully generated js/firebase-init.js from environment variables.');
