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
    <script src="https://www.gstatic.com/firebasejs/7.8.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.8.1/firebase-messaging.js"></script>
  </head>
<body>
  <a href="https://github.com/jcubic/chat" class="github-corner" aria-label="View source on GitHub"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>
<?php
require_once('Messages.php');

$messages = new Messages(0);
$data = $messages->fetch('username, message');

?>
<form>
<textarea readonly><?php
foreach ($data as $row) {
  $msg = htmlentities($row['message'], ENT_QUOTES, 'UTF-8');
  echo $row['username'] . "> " . $msg . "\n";
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
       if (Notification.permission === "granted") {
           messaging.getToken().then(handleTokens);
       } else {
           Notification.requestPermission().then(function() {
               return messaging.getToken();
           }).then(handleTokens);
       }
       function handleTokens(token) {
           messaging.onTokenRefresh(() => {
              messaging.getToken().then(updateToken);
           });
           updateToken(token);
       }
       function updateToken(token) {
           var data = new FormData();
            data.append('username', username);
            data.append('token', token);
            return fetch('register.php', {
                body: data,
                method: 'POST'
            }).then(r => r.text());
       }
   });
}

<?php } ?>
    </script>
  </body>
</html>
