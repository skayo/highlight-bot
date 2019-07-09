<?php

// This file gets executed every 5 seconds and handles everything

require_once 'config.php';

require_once 'lib/DevRant.php';

function botLog ($msg) {
	if (DEBUG) echo 'Bot > ' . $msg . PHP_EOL;
}

$devRant = new DevRant();

$lastCheckTime = file_exists(BOT_LAST_CHECK_TIME_FILE) ?
	intval(file_get_contents(BOT_LAST_CHECK_TIME_FILE)) : time();

file_put_contents(BOT_LAST_CHECK_TIME_FILE, time());

$notifications = $devRant->getNotifications();

usort($notifications['items'], function ($a, $b) {
	return $a['created_time'] <=> $b['created_time'];
});

$didSomething = false;

foreach ($notifications['items'] as $notification) {
	if ($notification['created_time'] > $lastCheckTime) {
		switch ($notification['type']) {
			case 'comment_vote':
				if ($notification['rant_id'] === THEME_SELECTION_RANT_ID) {
					require_once 'lib/ThemeSelection.php'; // Require here so it's not imported when not needed

					$didSomething = true;

					botLog('Handling a theme selection notif (User: ' . $notification['uid'] . ')...');
					ThemeSelection::handleNotif($devRant, $notification['uid'], $notification['comment_id']);
				}
				break;

			case 'comment_mention':
				$didSomething = true;

				botLog('Handling a code highlighting notif (CommentID: ' . $notification['comment_id'] . ')...');
				break;
		}
	}
}

if ($didSomething) {
	$devRant->clearNotifications();
}