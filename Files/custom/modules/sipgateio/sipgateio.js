$(document).ready(function () {
	var sugarApiSearch = function (call, success) {
		var apiData = $("#sipgateio");
		var callSearch;

		callSearch = "%" + call.from.split("").join("%");
		$.ajax({
			url: apiData.attr("data-baseurl") + "/service/v4_1/rest.php",
			type: "POST",
			data: {
				method: "search_by_module",
				input_type: "JSON",
				response_type: "JSON",
				rest_data: JSON.stringify({
					"session": apiData.attr("data-session"),
					"search_string": callSearch,
					"modules": [
						"Accounts",
						"Contacts",
						"Leads"
					],
					"offset": 0,
					"max_results": 1,
					"id": "",
					"select_fields": [
						"id",
						"name"
					],
					"favorites": false
				})
			},
			dataType: "json",
			success: function (result) {
				if (result) {

					result.entry_list.sort(function (a, b) {
						return b.records.length - a.records.length;
					});

					var module = result.entry_list[0];

					if (module.records.length) {
						success(call, {
							id: module.records[0].id.value,
							name: module.records[0].name.value,
							module: module.name,
							baseUrl: apiData.attr("data-baseurl")
						});
					}
					else {
						success(call, {});
					}
				}
			}
		});
	};

	var poll = function () {
		$.ajax({
			url: $("#sipgateio").attr("data-baseurl") + '/custom/modules/sipgateio/sipgateio.php',
			type: "GET",
			dataType: "json",
			success: function (result) {
				if(result) {
					var eventLog = eventLogToCalls(result);
					if (!$.isEmptyObject(eventLog)) {
						showCalls(eventLog);
					}
				}
				setTimer();
			}
		});
	};

	var eventLogToCalls = function (eventLog) {
		var calls = {};

		eventLog.sort(function (a, b) {
			return a.timestamp - b.timestamp;
		});

		for (var i = 0; i < eventLog.length; i++) {
			var event = eventLog[i];

			if (event["event"] === "newCall") {
				calls[event["callId"]] = event;
			}

			if (event["event"] === "answer") {
				calls[event["callId"]] = event;
				calls[event["callId"]].from = getNewCallInEventLog(eventLog, event["callId"]);
				var callElement = $("#ioCall_" + event["callId"]).find('.callState');
				callElement.removeClass('ringing');
				callElement.addClass('answered');
			}

			if (event["event"] === "hangup") {
				$("#ioCall_" + event["callId"]).fadeOut(300, function () {
					$(this).remove();
				});
				delete calls[event["callId"]];
			}
		}

		return calls;
	};

	var getNewCallInEventLog = function (eventLog, callId) {
		for (var i = 0; i < eventLog.length; i++) {
			var event = eventLog[i];
			if (event.callId === callId && event.from) {
				return event.from;
			}
		}
	};

	var showCalls = function (calls) {
		var callIds = Object.keys(calls);

		for (var i = 0; i < callIds.length; i++) {
			var call = calls[callIds[i]];

			if (!$("#ioCall_" + callIds[i]).length) {
				if (call.from) {
					sugarApiSearch(call, generateCallElementBox);
				}
			}
		}
	};

	var generateLink = function (result) {
		var link = document.createElement("a");
		link.href = result.baseUrl + "index.php?module=" + result.module + "&action=DetailView&record=" + result.id;
		link.appendChild(document.createTextNode(result.name));
		return link;
	};

	var generateCallElementBox = function (call, contact) {
		var callState = (call.event === 'newCall') ? 'ringing' : 'answered';

		var link = "#";
		var contactName = "Unknown Caller";
		if (!$.isEmptyObject(contact)) {
			link = generateLink(contact);
			contactName = contact.name;
		}
		var callInfoBox = "<li id='ioCall_" + call.callId + "'>\
            <a href='" + link + "'>\
                <span class='callState " + callState + "'>\
                    <span class='callStateInner'/>\
                </span>\
                <div class='callInfo'>\
                    <span class='callDirection'>Incoming Call</span>\
                    <span class='name'>" + contactName + "</span>\
                    <span class='number'>" + call.from + "</span>\
                </div>\
            </a>\
        </li>";

		$('#sipgateio').find('ul').append(callInfoBox);

		call = $("#ioCall_" + call.callId);

		window.setTimeout(function () {
			call.toggleClass('active');
		}, 10);

		window.setTimeout(function () {
			call.toggleClass('active collapse');
		}, 3000);
	};

	var setTimer = function () {
		setTimeout(function () {
			poll();
		}, 3000);
	};

	poll();
});