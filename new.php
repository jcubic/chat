<?php

if (isset($_POST['message']) && isset($_POST['username'])) {
  require_once('Messages.php');

  if (is_spam($_POST['message']) || is_spam($_POST['username'])) {
    die;
  }
  $messages = new Messages();
  $messages->newMessage($_POST['username'], $_POST['message']);
}



function is_spam($string) {
  if (preg_match("/\\b[13][a-km-zA-HJ-NP-Z1-9]{25,34}\\b/", $string)) {
    return true;
  }
  if (preg_match("/\\b(BTC|bitcoin|crypto|donate|Support me)\\b/i", $string)) {
    return true;
  }
  return false;
}
