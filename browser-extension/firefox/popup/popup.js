document.addEventListener("DOMContentLoaded", function() {
	//read data from server
	browser.runtime.sendMessage({
		action: "getData"
	});

	//listen call from backround.js
	browser.runtime.onMessage.addListener(function(message, sender, sendResponse) {
		hideLoader();
		if (message.action === "returnData") {
			var options = {
				valueNames: ['id', 'name', 'link', 'user', 'password'],
				item: '<tr><td class="id hidden"></td><td><strong class="name"></strong><span class="link hidden"></span><p class="user"></p></td><td align="right"><button type="button" class="copyButton"><span class="hidden password"></span><img src="password_icon.svg" width="20px" alt="Copy password"></button></td><tr>'
			};

			// Init list
			var passwordList = new List('passwordList', options);

			passwordList.add(message.data, function(items) {
				console.log('All items were loaded');
				browser.tabs.query({
					active: true,
					currentWindow: true
				}, function(tabs) {
					var currentTab = tabs[0];
					if (currentTab) {
						tabUrlObj = new URL(currentTab.url);
						passwordList.search(tabUrlObj.hostname.replaceAll('.', ''), ['link']);
					}
				});

			});

			document.getElementById("opendb").addEventListener("click", function() {
				const hashpassword = new Hashes.MD5().hex(document.getElementById("main-password").value);
				browser.storage.local.get("getUrl", function(result) {
					getUrl = result.getUrl || "";
					urlObj = new URL(getUrl);
					browser.runtime.sendMessage({
						action: "setCookie",
						url: urlObj.origin,
						name: "user-key",
						value: hashpassword,
						domain: urlObj.hostname,
						expirationDate: Math.floor(Date.now() / 1000) + (60 * 60 * 24) // Expires in 1 day
					});
					statusMessage.innerText = 'Main password inserted';
					setTimeout(function() {
						statusMessage.innerText = '';
					}, 3000)
				});
			});

			var copyButtons = document.querySelectorAll(".copyButton");
			copyButtons.forEach(function(button) {
				button.addEventListener("click", function() {
					browser.storage.local.get("getUrl", function(result) {
						getUrl = result.getUrl || "";
						urlObj = new URL(getUrl);
						chrome.cookies.get({
							url: urlObj.origin,
							name: "user-key"
						}, function(cookie) {
							if (cookie) {
								const key = cookie.value;
								decryptString(button.childNodes[0].innerText, key).then((decryptedString) => {
									copyTextToClipboard(decryptedString);
									statusMessage.innerText = 'Pasword copied';
									setTimeout(function() {
										statusMessage.innerText = '';
									}, 3000)
								}).catch(e => {
									statusMessage.innerText = 'Password mismatch';
									setTimeout(function() {
										statusMessage.innerText = '';
									}, 3000)
								});
							} else {
								statusMessage.innerText = 'Insert main password first';
								setTimeout(function() {
									statusMessage.innerText = '';
								}, 3000)
							}
						});
					});

				});
			});
		}
	});

	//copy to clipboard function
	function copyTextToClipboard(text) {
		var sampleTextarea = document.createElement("textarea");
		document.body.appendChild(sampleTextarea);
		sampleTextarea.value = text; //save main text in it
		sampleTextarea.select(); //select textarea contenrs
		document.execCommand("copy");
		document.body.removeChild(sampleTextarea);
	}

	//loader function
	function showLoader() {
		loader.classList.remove("hidden");
	}

	function hideLoader() {
		loader.classList.add("hidden");
	}
});