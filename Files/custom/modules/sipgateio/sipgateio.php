<?php

session_id("sipgateio");
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
	handleSipgateIOEvent();
} else {
	handleSugarEventPoll();
}

function writeToEventFile($data)
{
    if (!isset($_SESSION["eventlog"])) {
        $_SESSION["eventlog"] = [];
    }

	$eventLog = $_SESSION["eventlog"];

	if (count($eventLog) > 20) {
		array_shift($eventLog);
	}
	array_push($eventLog, $data);

    $_SESSION["eventlog"] = $eventLog;
}

function readFromEventFile()
{
    if (!isset($_SESSION["eventlog"])) {
        $_SESSION["eventlog"] = [];
    }

	return $_SESSION["eventlog"];
}

function postVal($key)
{
	return (array_key_exists($key, $_POST) ? $_POST[$key] : null);
}

function handleSipgateIOEvent()
{
	if (postVal("direction") == "in" || postVal("direction") === null) {
		writeToEventFile(array(
				"event" => postVal("event"),
				"user" => postVal("user"),
				"direction" => postVal("direction"),
				"from" => postVal("from"),
				"to" => postVal("to"),
				"callId" => postVal("callId"),
				"diversion" => postVal("diversion"),
				"cause" => postVal("cause"),
				"timestamp" => microtime(true)
			)
		);

		$url = 'http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
		header("Content-type: application/xml");
		echo "<Response onAnswer=\"$url\" onHangup=\"$url\"></Response>";
	} else {
		header("Content-type: application/xml");
		echo "<Response></Response>";
	}
}

function handleSugarEventPoll()
{
	header("Content-type: application/json");
	echo json_encode(readFromEventFile());
}
