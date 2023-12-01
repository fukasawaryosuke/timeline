// Import the functions you need from the SDKs you need
import { initializeApp } from 'firebase/app';
import { getAnalytics, isSupported } from 'firebase/analytics';
import {
  getMessaging,
  getToken,
  onMessage,
  MessagePayload,
} from 'firebase/messaging';

const useFirebase = () => {
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: process.env.MIX_FIREBASE_API_KEY,
    authDomain: process.env.MIX_FIREBASE_AUTH_DOMAIN,
    projectId: process.env.MIX_FIREBASE_PROJECT_ID,
    storageBucket: process.env.MIX_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: process.env.MIX_FIREBASE_MESSAGE_SENDER_ID,
    appId: process.env.MIX_FIREBASE_APP_ID,
    measurementId: process.env.MIX_FIREBASE_MEASUREMENT_ID,
  };

  const isSupported = () => {
    if (!('Notification' in window)) {
      return false;
    } else {
      return true;
    }
  };

  const initFirebase = async (): Promise<string> => {
    // Initialize Firebase
    const app = initializeApp(firebaseConfig); // NOTE: service workerの登録はFirebaseが自動で行ってくれている
    const analytics = getAnalytics(app);
    const messaging = getMessaging(app);
    const vapidKey = {
      vapidKey: process.env.MIX_FIREBASE_CERT_KEY,
    };
    let deviceToken = '';

    await getToken(messaging, vapidKey)
      .then((currentToken) => {
        if (currentToken) {
          console.log(currentToken);
          // ACTIVE時にプッシュを受信した時のロジック
          onMessage(messaging, (payload: MessagePayload): void => {
            console.log('received push');
            if (Notification.permission !== 'granted') return;
            let notificationTitle = '';
            const notificationOptions: NotificationOptions = {
              body: '',
              icon: '/images/icon64.png',
              data: {
                url: '',
              },
            };
            if (payload.data) {
              notificationTitle =
                payload.data['pinpoint.notification.title'] ??
                'Background Message Title';
              notificationOptions.body =
                payload.data.default ??
                payload.data['pinpoint.notification.body'];
              notificationOptions.data.url =
                payload.data['pinpoint.url'] ?? process.env.MIX_APP_URL;
            }
            navigator.serviceWorker.ready.then((registratioin) => {
              return registratioin.showNotification(
                notificationTitle,
                notificationOptions
              );
            });
          });
          deviceToken = currentToken;
        }
      })
      .catch((err) => {
        console.log('get token error');
        console.dir(err);
      });
    return deviceToken;
  };

  // 端末情報取得
  const getDeviceType = () => {
    if (isiOS()) {
      return 'iOS';
    } else if (isAndroid()) {
      return 'Android';
    } else {
      return 'Browser';
    }
  };

  // プッシュのチャンネルタイプ取得
  const getChannelType = () => {
    if (isiOS()) {
      return 'APNS';
    } else if (isAndroid()) {
      return 'GCM';
    } else {
      // NOTE: ブラウザ
      return 'GCM';
    }
  };

  // モバイル端末か確認
  const isMobile = () => {
    return isiOS() || isAndroid();
  };

  /** private method */
  const isiOS = () => {
    return (
      navigator.userAgent.indexOf('iPhone') > 0 ||
      navigator.userAgent.indexOf('iPad') > 0
    );
  };
  const isAndroid = () => {
    return navigator.userAgent.indexOf('Android') > 0;
  };

  return { isSupported, initFirebase, getDeviceType, getChannelType, isMobile };
};

export default useFirebase;
