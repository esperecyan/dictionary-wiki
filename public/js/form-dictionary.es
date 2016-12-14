
(function () {
'use strict';

/**
 * 対象のフォーム。
 * @type {HTMLFormElement}
 */
let form = document.forms.dictionary;

/**
 * 辞書の作成方法を切り替えるラジオボタン。
 * @type {?RadioNodeList}
 */
let uploadings = form.uploading;

/**
 * ラジオボタンのチェックに基づいて、fieldsetの有効・無効を切り替えます。
 */
function switchFieldsets()
{
	for (let i = 0, l = uploadings.length; i < l; i++) {
		let fieldSet = uploadings[i].closest('fieldset');
		let enabled = uploadings[i].checked;
		fieldSet.disabled = !enabled;
		$(fieldSet.getElementsByClassName('panel-body')[0]).collapse(enabled ? 'show' : 'hide');
		
		// bootstrap-validator は fieldset 要素の disabled 属性に対応していない
		fieldSet.querySelector('[name="dictionary"], [name="csv"]').required = enabled;
	}
	$(form).validator('update');
}

// スクリプト有効時の機能に関するWAI-ARIA属性の付加
if (uploadings) {
	for (let i = 0, l = uploadings.length; i < l; i++) {
		let fieldSet = uploadings[i].closest('fieldset');
		let list = fieldSet.getElementsByClassName('panel-body')[0];
		uploadings[i].setAttribute('aria-controls', [fieldSet, list].map(function (element) {
			return element.id;
		}).join(' '));
	}
}

// Bootstrap用にマークアップを修正
let panelBodies = document.querySelectorAll('fieldset > .panel-body');
for (let i = 0, l = panelBodies.length; i < l; i++) {
	panelBodies[i].classList.add('collapse');
}

let tabs = document.querySelectorAll('.nav.nav-tabs[role="tablist"] > li > [role="tab"]');
for (let i = 0, l = tabs.length; i < l; i++) {
	tabs[i].dataset.toggle = 'tab';
	tabs[i].href = '#' + tabs[i].getAttribute('aria-controls');
	tabs[i].removeAttribute('aria-disabled');
	tabs[i].parentElement.classList.remove('disabled');
}
$(tabs).on('shown.bs.tab', function (event) {
	event.target.setAttribute('aria-selected', 'true');
	event.relatedTarget.removeAttribute('aria-selected');
});

$(form).validator();

// 表形式で編集
let table = document.getElementById('table');

let items = {};
let options = document.getElementById('table-context-menu').options;
for (let i = 0, l = options.length; i < l; i++) {
	items[options[i].value] = options[i].text === '---------' ? options[i].text : {name: options[i].text};
}

let handsontable;

/**
 * 「表形式で編集」タブの内容を、テキストエリアに反映します。
 * @param {boolean} stripEmptyRow - 空行を取り除くなら真。
 */
function tableToCSV(stripEmptyRow)
{
	let data = handsontable.getData();
	form.csv.value = Papa.unparse(stripEmptyRow ? data.filter(function (row) {
		return row.join('') !== '';
	}) : data);
	handsontable.destroy();
}

// CSVのタブ切り替え
$('[aria-controls="source"], [aria-controls="table"]').on('show.bs.tab', function (event) {
	if (event.target.getAttribute('aria-controls') === 'table') {
		let csvErrors = document.getElementById('csv-errors');
		while (csvErrors.hasChildNodes()) {
			csvErrors.firstChild.remove();
		}

		let csv = form.csv;
		let result = Papa.parse(csv.value, {delimiter: ','});
		if (result.errors.length > 0) {
			event.preventDefault();
			Element.prototype.append.apply(csvErrors, result.errors.map(function (error) {
				let item = document.createElement('li');
				item.setAttribute('role', 'alert');
				item.textContent = error.code + ': ' + error.message + ' :' + (error.row + 1);
				item.classList.add('alert', 'alert-danger');
				return item;
			}));
		} else {
			let data = [['text', 'answer', 'description']];
			if (result.data.length > 0) {
				data = result.data;
				if (!result.data[0].includes('text')) {
					data.unshift(['text'].concat(Array(Math.max.apply(result.data.map(function (row) {
						return row.length;
					}))).fill('answer')));
				}
			} else {
				data = Papa.parse(csv.defaultValue).data;
			}
			if (data[0].length > 6) {
				table.closest('.tab-content').classList.add('many-columns');
			}
			handsontable = new Handsontable(table, {
				data: data,
				minCols: 1,
				minRows: 5,
				minSpareRows: 1,
				height: Number.NaN,
				contextMenu: {items: items},
				tableClassName: ['table', 'table-hover', 'table-striped'],
			});
		}
	} else {
		tableToCSV();
	}
});

// 既定で「表形式で編集」タブを選択
let tableTab = document.querySelector('[aria-controls="table"]');
tableTab.href = '#table';
$(tableTab).tab('show');

if (uploadings) {
	// フィールドセットの切り替え
	switchFieldsets();
	for (let i = 0, l = uploadings.length; i < l; i++) {
		uploadings[i].addEventListener('change', switchFieldsets);
	}
}

// 投稿時
let submitButton = form.querySelector('button:not([type])');
form.addEventListener('submit', function (event) {
	if (!event.defaultPrevented) {
		let uploadingRadioButton = event.target.querySelector('[name="uploading"][value="1"]');
		if (!(uploadingRadioButton && uploadingRadioButton.checked)
			&& event.target.querySelector('[aria-controls="source"]').getAttribute('aria-selected') !== 'true') {
			tableToCSV(true);
		}

		submitButton.disabled = true;
		submitButton.innerHTML = '<i class="fa fa-btn fa-spinner fa-pulse"></i>';
		submitButton.appendChild(new Text(submitButton.dataset.progressMessage));
	}
});
window.addEventListener('pageshow', function () {
	submitButton.disabled = false;
	submitButton.textContent = submitButton.dataset.defaultMessage;
});

})();
