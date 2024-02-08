document.addEventListener("DOMContentLoaded", function() {
	// Load settings on page load
	loadSettings();

	// Save button click event
	document.getElementById("saveButton").addEventListener("click", function() {
		saveSettings();
	});
});

// Load settings from storage and populate the form
function loadSettings() {
	chrome.storage.local.get("getUrl", function(result) {
		document.getElementById("getUrl").value = result.getUrl || "";
	});

	chrome.storage.local.get("apiKey", function(result) {
		document.getElementById("apiKey").value = result.apiKey || "";
	});
}

// Save settings to storage
function saveSettings() {
	var apiKey = document.getElementById("apiKey").value;
	var getUrl = document.getElementById("getUrl").value;

	chrome.storage.local.set({
		"apiKey": apiKey
	}, function() {
		console.log("Settings saved:", apiKey);
		statusMessage.innerText = 'Settings saved';
		setTimeout(function() {
			statusMessage.innerText = '';
		}, 3000)
	});

	chrome.storage.local.set({
		"getUrl": getUrl
	}, function() {
		console.log("Settings saved:", getUrl);
		statusMessage.innerText = 'Settings saved';
		setTimeout(function() {
			statusMessage.innerText = '';
		}, 3000)
	});
}