self.addEventListener('push', (event) => {
    const payload = event.data.json();
    console.log(
        '[firebase-messaging-sw.js] Received background message ',
        payload
    );
    // Customize notification here
    const notificationTitle =
        payload.data['pinpoint.notification.title'] ??
        'Background Message Title';
    const notificationBody =
        payload.data.default ?? payload.data['pinpoint.notification.body'];
    const notificationOptions = {
        body: notificationBody,
        icon: '/images/icon64.png',
        data: {
            url: payload.data['pinpoint.url'] ?? process.env.MIX_APP_URL,
        },
    };
    console.log('before event waitUntil');
    event.waitUntil(
        self.registration
            .showNotification(notificationTitle, notificationOptions)
            .finally(() => {
                console.log('finally');
                return;
            })
    );
    console.log('after event waitUntil');
});

self.addEventListener('notificationclick', (event) => {
    console.log('notificationclick1');
    const redirectUrl = event.notification?.data.url ?? '';
    event.notification.close();
    event.waitUntil(
        self.clients
            .matchAll({
                type: 'window',
            })
            .then(function (clientList) {
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i];
                    if (client.url == redirectUrl && 'focus' in client) {
                        return client.focus();
                    }
                }
                console.log('open window1');
                return self.clients.openWindow(redirectUrl);
            })
            .catch(function (err) {
                console.log(err);
                return;
            })
    );
});
