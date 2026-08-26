// Firebase Configuration & Initialization for Construction Helps
var firebaseConfig = {
  apiKey: atob("QUl6YVN5REYzWkxDRjdGWnFXRWJnMGJ6eENvODQ3UFFKczNYTHlN"),
  authDomain: "digieleven-e7def.firebaseapp.com",
  projectId: "digieleven-e7def",
  storageBucket: "digieleven-e7def.firebasestorage.app",
  messagingSenderId: "670060380676",
  appId: "1:670060380676:web:03d984d8aa4b39ea162b51",
  measurementId: "G-1V3W2ENNGD"
};

// Initialize Firebase App & Firestore
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
