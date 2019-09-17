<?php

if (isset($_POST['message']) && isset($_POST['username'])) {
  require_once('Messages.php');

  $messages = new Messages();
  $messages->newMessage($_POST['username'], $_POST['message']);
}

