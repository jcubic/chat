<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 'On');

define("__DEBUG__", false);

?><!DOCTYPE html>
<html>
  <head>
    <title>Simple PHP Chat</title>
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <meta name="description" content="Prosty czat w JavaScript i PHP, za pomocą Server Side Events"/>
    <link href="style.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://www.gstatic.com/firebasejs/7.5.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.5.0/firebase-messaging.js"></script>
  </head>
<body>
<?php
require_once('Messages.php');

$messages = new Messages(0);
$data = $messages->fetch('username, message');

?>
<form>
<textarea readonly><?php
foreach ($data as $row) {
  echo $row['username'] . "> " . $row['message'] . "\n";
}
?></textarea>
<input placeholder="what you want to say?"/>
</form>
<script>
function send(username, message) {
   const data = new URLSearchParams();
   data.append('username', username);
   data.append('message', message);
   return fetch('new.php', {method: 'POST', body: data}).then(r => r.text());
}
const textarea = document.getElementsByTagName('textarea')[0];
const input = document.getElementsByTagName('input')[0];
const form = document.getElementsByTagName('form')[0];
let username;
while (true) {
    username = prompt("What's your name?");
    if (typeof username === 'string') {
        username = username.trim();
        if (username) {
            break;
        }
    }
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    send(username, input.value);
    input.value = '';
});
const eventSource = new EventSource('stream.php');
eventSource.addEventListener('chat', (e) => {
    var data = JSON.parse(e.data);
    textarea.value += data.username + '> ' + data.message + '\n';
    textarea.scrollTop = textarea.scrollHeight;
});

textarea.scrollTop = textarea.scrollHeight;
input.focus();

<?php if (isset($_GET['notification'])) { ?>
// Firebase Code
var firebaseConfig = {
    apiKey: "AIzaSyBJguGFPPZXozdkPVpBZNbGMVJ_LTOYuQA",
    authDomain: "jcubic-1500107003772.firebaseapp.com",
    databaseURL: "https://jcubic-1500107003772.firebaseio.com",
    projectId: "jcubic-1500107003772",
    storageBucket: "jcubic-1500107003772.appspot.com",
    messagingSenderId: "1005897028349",
    appId: "1:1005897028349:web:f9f90304397535db17e494"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

if ('serviceWorker' in navigator) {
   navigator.serviceWorker.register('sw.js', {
       scope: './'
   }).then((registration) => {
       firebase.messaging().useServiceWorker(registration);
       const messaging = firebase.messaging();
       messaging.requestPermission().then(function() {
           return messaging.getToken();
       }).then(token => {
           messaging.onTokenRefresh(() => {
              messaging.getToken().then((refreshedToken) => {
                  // when token is refreshed update token on server
                  // for this, you need to get id of the user from server
                  // in register function so you can update
              });
           });
           var data = new FormData();
           messaging.onMessage((payload) => {
               console.log('Message received. ', payload);
           });
           data.append('username', username);
           data.append('token', token);
           return fetch('register.php', {
              body: data,
              method: 'POST'
           }).then(r => r.text());
       });
   });
}

<?php } ?>
    </script>
  </body>
</html>
