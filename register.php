<?php

if (isset($_POST['username']) && isset($_POST['token'])) {
    require_once('Notifications.php');
    $notification = new Notification();

    $notification->register($_POST['username'], $_POST['token']);
}