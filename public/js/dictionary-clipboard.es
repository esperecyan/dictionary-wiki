(function () {
'use strict';

let ClipboardManager = {
	constructor: function ()
	{
		let parent = document.getElementById('copy-buttons');
		
		/**
		 * @member {HTMLDivElement}
		 * @access protected
		 */
		this.alert = parent.querySelector('[role="alert"]');
		
		/**
		 * @member {NodeList.<HTMLButtonElement>}
		 * @access protected
		 */
		this.buttons = document.getElementsByName('copy');
		
		parent.getElementsByTagName('ul')[0].addEventListener('click', this);
		window.addEventListener('copy', this);
		parent.removeAttribute('title');
		
		this.enableButtons();
	},
	
	inMicrosoftEdge: function ()
	{
		return typeof chrome !== 'undefined' && !('runtime' in chrome);
	},
	
	handleEvent: function (event)
	{
		switch (event.type) {
			case 'click':
				if (event.target.name !== 'copy' && this.copying) {
					return;
				}
				this.alert.textContent = '';
				this.alert.classList.remove('alert', 'alert-success', 'alert-danger');
				if (typeof chrome === 'undefined') {
					event.target.getElementsByClassName('fa')[0].classList.add('fa-spinner', 'fa-pulse');
				}
				
				/**
				 * @member {HTMLButtonElement}
				 * @access protected
				 */
				this.button = event.target;
				document.execCommand('copy');
				break;
				
			case 'copy':
				if (this.button && !this.copying) {
					this.copying = true;
					this.disableButtons();
					event.preventDefault();
					
					let url = new URL(location);
					//url.searchParams.set('type', this.button.value);
					url.search = '?type=' + this.button.value + (this.button.value === 'quiz' ? '&scope=text' : '');
					if (this.inMicrosoftEdge() && ['quiz', 'siri'].includes(this.button.value)) {
						event.clipboardData.setData('text', url);
					}
					let client = new XMLHttpRequest();
					client.open('GET', url, false);
					client.send();
					
					if (client.status === 200) {
						let clipboardValue;
						switch (this.button.value) {
							case 'quiz':
							case 'siri':
								clipboardValue = url;
								break;
							case 'pictsense':
								clipboardValue = client.responseText;
								break;
						}
						try {
							event.clipboardData.setData('text', clipboardValue);
						} catch (exception) {
							if (this.inMicrosoftEdge() && exception.message.startsWith('Access is denied.')) {
								console.error(exception);
								if (!['quiz', 'siri'].includes(this.button.value)) {
									this.alert.classList.add('alert', 'alert-danger');
									this.alert.textContent = this.alert.dataset.failure;
								}
							} else {
								throw exception;
							}
						}
						if (!this.alert.classList.contains('alert')) {
							this.alert.classList.add('alert', 'alert-success');
							this.alert.textContent = this.alert.dataset.success;
						}
						this.enableButtons();
					} else {
						let message;
						if (client.getResponseHeader('content-type').includes('json')) {
							let details = JSON.parse(client.responseText);
							message = details.status + ' ' + details.title + ': ' + details.detail;
						} else {
							message = client.status + ' ' + client.statusText;
						}
						this.alert.classList.add('alert', 'alert-danger');
						this.alert.textContent = message;
						this.enableButtons([this.button]);
					}
					this.button.getElementsByClassName('fa')[0].classList.remove('fa-spinner', 'fa-pulse');
					this.button = null;
					setTimeout(function () {
						this.copying = false;
					}.bind(this));
				}
				break;
		}
	},

	/**
	 * すべてのコピーボタンを無効化します。
	 */
	disableButtons: function ()
	{
		this.buttons = document.querySelectorAll('[name="copy"]:not([disabled])');
		for (let i = 0, l = this.buttons.length; i < l; i++) {
			this.buttons[i].disabled = true;
		}
	},

	/**
	 * 無効化前に有効だったすべてのコピーボタンを有効化します。
	 * @param {HTMLButtonElement[]} [exclusions] 無効化したままにするボタン。
	 */
	enableButtons: function (exclusions)
	{
		if (!exclusions) {
			exclusions = [];
		}
		for (let i = 0, l = this.buttons.length; i < l; i++) {
			if (!exclusions.includes(this.buttons[i])) {
				this.buttons[i].disabled = false;
			}
		}
	},
};

ClipboardManager.constructor();

})();
