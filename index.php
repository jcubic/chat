<!DOCTYPE html>
<html>
  <head>
    <title>Simple PHP Chat</title>
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <meta name="description" content="Prosty czat w JavaScript i PHP, za pomocą Server Side Events"/>
    <link href="style.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    </script>
  </body>
</html>
