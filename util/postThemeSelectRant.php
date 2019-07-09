<?php

// This file is used for setting up the bot
// It posts a rant and many comments which are used to select the theme

require_once 'config.php';

require_once 'lib/DevRant.php';
require_once 'lib/ImageGenerator.php';

$devRant = new DevRant();
$imageGenerator = new ImageGenerator();

// Post Rant
$rantText = file_get_contents('util/themeSelectRant.txt');
$rantID = $devRant->postRant($rantText, ['syntax', 'highlight', 'bot', 'tool']);

// Post all comments
$themes = (require 'themes.php');
foreach ($themes as $theme) {
	$devRant->postComment($rantID, $theme['name']);
}

// Store rant id
$configPHP = file_get_contents('config.php');
$configPHP = str_replace(
	'define(\'THEME_SELECTION_RANT_ID\', ' . THEME_SELECTION_RANT_ID . ');',
	'define(\'THEME_SELECTION_RANT_ID\', ' . $rantID . ');',
	$configPHP
);
file_put_contents('config.php', $configPHP);