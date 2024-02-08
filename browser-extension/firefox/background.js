//message on extension install
browser.runtime.onInstalled.addListener(function() {
	console.log("Buddypassword Installed");
});

//listen call from popup.js
browser.runtime.onMessage.addListener(function(message, sender, sendResponse) {
	let apiKey;

	browser.storage.local.get("apiKey", function(result) {
		apiKey = result.apiKey || "";
		console.log("API Key loaded in background script:", apiKey);
		if (message.action === "getData") {
			browser.storage.local.get("getUrl", function(result) {
				getUrl = result.getUrl || "";
				console.log("getUrl loaded in background script:", getUrl);
				readData(getUrl, apiKey);
			});
		} else if (message.action === "setCookie") {
			// Set the cookie
			browser.cookies.set({
				url: message.url,
				name: message.name,
				value: message.value,
				domain: message.domain,
				expirationDate: message.expirationDate
			});
		}
	});
});

//functions
function readData(postUrl, apiKey) {
	fetch(postUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + apiKey,
			}
		})
		.then(response => {
			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			browser.runtime.sendMessage({
				action: "returnData",
				data: data
			});
		})
		.catch(error => {
			console.error('Error:', error.message);
			browser.runtime.sendMessage({
				action: "dataNotReturn",
				data: error.message
			});
		});
}