/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************************!*\
  !*** ./resources/js/firebaseServiceWorker.js ***!
  \***********************************************/
self.addEventListener('push', function (event) {
  var _payload$data$pinpoin, _payload$data$default, _payload$data$pinpoin2;

  var payload = event.data.json();
  console.log('[firebase-messaging-sw.js] Received background message ', payload); // Customize notification here

  var notificationTitle = (_payload$data$pinpoin = payload.data['pinpoint.notification.title']) !== null && _payload$data$pinpoin !== void 0 ? _payload$data$pinpoin : 'Background Message Title';
  var notificationBody = (_payload$data$default = payload.data["default"]) !== null && _payload$data$default !== void 0 ? _payload$data$default : payload.data['pinpoint.notification.body'];
  var notificationOptions = {
    body: notificationBody,
    icon: '/images/icon64.png',
    data: {
      url: (_payload$data$pinpoin2 = payload.data['pinpoint.url']) !== null && _payload$data$pinpoin2 !== void 0 ? _payload$data$pinpoin2 : "http://localhost"
    }
  };
  console.log('before event waitUntil');
  event.waitUntil(self.registration.showNotification(notificationTitle, notificationOptions)["finally"](function () {
    console.log('finally');
    return;
  }));
  console.log('after event waitUntil');
});
self.addEventListener('notificationclick', function (event) {
  var _event$notification$d, _event$notification;

  console.log('notificationclick1');
  var redirectUrl = (_event$notification$d = (_event$notification = event.notification) === null || _event$notification === void 0 ? void 0 : _event$notification.data.url) !== null && _event$notification$d !== void 0 ? _event$notification$d : '';
  event.notification.close();
  event.waitUntil(self.clients.matchAll({
    type: 'window'
  }).then(function (clientList) {
    for (var i = 0; i < clientList.length; i++) {
      var client = clientList[i];

      if (client.url == redirectUrl && 'focus' in client) {
        return client.focus();
      }
    }

    console.log('open window1');
    return self.clients.openWindow(redirectUrl);
  })["catch"](function (err) {
    console.log(err);
    return;
  }));
});
/******/ })()
;
//# sourceMappingURL=firebase-messaging-sw.js.map