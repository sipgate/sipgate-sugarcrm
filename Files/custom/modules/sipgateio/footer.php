<?php


if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class SipgateIO
{
	function after_ui_footer_method($event, $arguments)
	{
		if (!isset($_REQUEST["to_pdf"]) || $_REQUEST["to_pdf"] == false) {
			$_SESSION['is_valid_session'] = true;
			$_SESSION['type'] = "user";
			$_SESSION['ip_address'] = $_SESSION['ipaddress'];
			echo '<link rel="stylesheet" type="text/css" href="' . $this->currentBaseUrl() . '/custom/modules/sipgateio/sipgateio.css" />';
			echo '<div id="sipgateio" data-session="' . session_id() . '" data-baseurl="' . $this->currentBaseUrl() . '"><ul></ul></div><script src="' . $this->currentBaseUrl() . '/custom/modules/sipgateio/sipgateio.js"></script>';
		}
	}

	private function currentBaseUrl()
	{
		$url = 'http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}";

		if($_SERVER['REQUEST_URI']) {
			$url .= "/{$_SERVER['REQUEST_URI']}";
		}

		return preg_replace("_/[^/]*(\\?.*)?\$_", "", $url);
	}
}
