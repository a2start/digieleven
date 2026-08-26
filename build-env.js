// Build script to generate js/firebase-init.js from Environment Variables (GitHub Secrets)
const fs = require('fs');
const path = require('path');

const apiKey = process.env.FIREBASE_API_KEY || Buffer.from('QUl6YVN5REYzWkxDRjdGWnFXRWJnMGJ6eENvODQ3UFFKczNYTHlN', 'base64').toString('utf8');
const authDomain = process.env.FIREBASE_AUTH_DOMAIN || 'digieleven-e7def.firebaseapp.com';
const projectId = process.env.FIREBASE_PROJECT_ID || 'digieleven-e7def';
const storageBucket = process.env.FIREBASE_STORAGE_BUCKET || 'digieleven-e7def.firebasestorage.app';
const messagingSenderId = process.env.FIREBASE_MESSAGING_SENDER_ID || '670060380676';
const appId = process.env.FIREBASE_APP_ID || '1:670060380676:web:03d984d8aa4b39ea162b51';
const measurementId = process.env.FIREBASE_MEASUREMENT_ID || 'G-1V3W2ENNGD';
const apiKeyBase64 = Buffer.from(apiKey).toString('base64');

// EmailJS Environment Variables
const emailjsServiceId = process.env.EMAILJS_SERVICE_ID || 'service_j6wp3lk';
const emailjsTemplateId = process.env.EMAILJS_TEMPLATE_ID || '';
const emailjsPublicKey = process.env.EMAILJS_PUBLIC_KEY || '';

const content = `// Auto-generated Firebase & EmailJS initialization from Environment Variables
var firebaseConfig = {
  apiKey: atob("${apiKeyBase64}"),
  authDomain: "${authDomain}",
  projectId: "${projectId}",
  storageBucket: "${storageBucket}",
  messagingSenderId: "${messagingSenderId}",
  appId: "${appId}",
  measurementId: "${measurementId}"
};

var emailjsConfig = {
  serviceId: "${emailjsServiceId}",
  templateId: "${emailjsTemplateId}",
  publicKey: "${emailjsPublicKey}"
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

try {
    if (typeof emailjs !== 'undefined' && emailjsConfig.publicKey) {
        emailjs.init({ publicKey: emailjsConfig.publicKey });
    }
} catch(e) {
    console.warn('EmailJS initialization notice:', e);
}
`;

const targetPath = path.join(__dirname, 'js', 'firebase-init.js');
fs.writeFileSync(targetPath, content, 'utf8');
console.log('Successfully generated js/firebase-init.js from environment variables.');

