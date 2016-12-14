(function () {
'use strict';

document.querySelector('[action$="/diff"]').addEventListener('submit', function () {
	let revisions = document.getElementsByName('revisions[]');
	let checkedRevisions = document.querySelectorAll('[name="revisions[]"]:checked');
	switch (checkedRevisions.length) {
		case 0:
			revisions[0].checked = true;
			revisions[1].checked = true;
			break;
		case 1:
			let checkedRevision = checkedRevisions[0];
			if (revisions[revisions.length - 1] === checkedRevision) {
				revisions[revisions.length - 2].checked = true;
			} else {
				for (let i = 0, l = revisions.length, next = false; i < l; i++) {
					if (next) {
						revisions[i].checked = true;
						break;
					} else if (revisions[i] === checkedRevision) {
						next = true;
					}
				}
			}
			break;
		case 2:
			break;
		default:
			for (let i = 0, l = checkedRevisions.length; i < l - 1; i++) {
				if (i > 0) {
					checkedRevisions[i].checked = false;
					break;
				}
			}
	}
});

})();
