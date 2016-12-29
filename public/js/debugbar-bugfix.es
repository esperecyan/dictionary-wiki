(function () {
'use strict';

document.addEventListener('DOMContentLoaded', function () {
	let open = XMLHttpRequest.prototype.open;
	XMLHttpRequest.prototype.open = function () {
		if (1 in arguments) {
			arguments[1] = String(arguments[1]);
		}
		open.apply(this, arguments);
	};
});

})();
