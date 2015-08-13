<?php

if ($_SERVER['REQUEST_METHOD'] === "POST") {
	handleSipgateIOEvent();
} else {
	handleSugarEventPoll();
}

function getFilename()
{
	return sys_get_temp_dir() . "/ioevent.data";
}

function writeToEventFile($data)
{
	$fp = fopen(getFilename(), "a");

	if (flock($fp, LOCK_EX)) {
		$eventBuffer = unserialize(file_get_contents(getFilename()));
		ftruncate($fp, 0);

		if (!$eventBuffer || !is_array($eventBuffer)) {
			$eventBuffer = array();
		} else if (count($eventBuffer) > 20) {
			array_shift($eventBuffer);
		}

		array_push($eventBuffer, $data);

		fwrite($fp, serialize($eventBuffer));

		fflush($fp);
		flock($fp, LOCK_UN);
	}

	fclose($fp);
}

function readFromEventFile()
{
	$fp = fopen(getFilename(), "a");

	$data = "";

	if (flock($fp, LOCK_SH)) {

		$data = unserialize(file_get_contents(getFilename()));

		flock($fp, LOCK_UN);
	}

	fclose($fp);

	return $data;
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
