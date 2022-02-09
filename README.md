# Simple Chat WebApp

Simple, mobile friendly, real time Chat.

Created with AJAX, Server Side Events, PHP and SQLite.
With Help from Firebase Cloud Messaging

## Setup

to setup the app, you need to register firebase project then firebase web app
for that project. Then copy the Cloud Messaging Secret key and save in
`firebase_token` file.

You should also add your Firebase application keys to index.php and sw.js.

## Notifications

Check [notification branch](https://github.com/jcubic/chat/tree/notifications) that has new code that add push notification using firebase. The original code was left as is because of the blog article.

## Blog post

Explanation of the code is available in Polish (but you can translate the text using embedded widget):
* [Prosty Czat w JavaScript, PHP i SQLite](https://jcubic.pl/2019/09/prosty-czat-javascript-php-sqlite.html)
* [Powiadomienia - Push Notifications (aplikacja czatu)](https://jcubic.pl/2020/02/powiadomiena-push-notifications.html)

## License

The code released under MIT license<br/>
Copyright (C) 2019 Jakub T. Jankiewicz <<https://jcubic.pl/me>>
