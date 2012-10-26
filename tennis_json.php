<?php
include_once("index.php");

// json access
function jEncode($data) {
	header('Content-type: application/json; charset=utf-8');
	return json_encode($data);
}

function getMatch() {
	if (isset($_SESSION['match_thing']))
		return $_SESSION['match_thing'];
	return null;
}

function putMatch($mtch) {
	$_SESSION['match_thing'] = $mtch;
}

// main processing
session_start();

$cmd = getP('c');
switch ($cmd) {
	case "start":
		// start the match
		$srv = getP('service');
		jsStartMatch($srv);
		jEncode("match started");
		break;
		
	case "point":
		// add a point
		$tm = getP('team');
		jsAwardPoint($tm);
		break;
		
	case "score":
		// get the score
		jsGetScore();
		break;
		
	default:
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}

function jsStartMatch($service = 'a') {
	$oJSMatch = getMatch();
	$oJSMatch = new Tennis_Match($service);
	putMatch($oJSMatch);
}

function jsAwardPoint($id) {
	$oJSMatch = getMatch();
	$oJSMatch->pointScored($id, false);
	putMatch($oJSMatch);
	return jsGetScore();
}

function jsGetScore() {
	$oJSMatch = getMatch();
	$s = $oJSMatch->getGameScore();
	putMatch($oJSMatch);
	echo jEncode($s);
}