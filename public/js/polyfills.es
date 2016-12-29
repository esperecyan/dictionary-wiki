(function () {
'use strict';

// Polyfills for Microsoft Edge and Internet Explorer

if (typeof chrome !== 'undefined' && !('runtime' in chrome) || typeof clipboardData !== 'undefined') {
	let setData = DataTransfer.prototype.setData;
	DataTransfer.prototype.setData = function (format, data) {
		if (1 in arguments) {
			arguments[1] = String(arguments[1]);
		}
		setData.apply(this, arguments);
	};
}

// Polyfills for Internet Explorer

if (!Array.prototype.includes) {
  /**
   * Production steps of ECMA-262, Edition 6, 22.1.2.1
   * @see [Polyfill — Array.prototype.includes() — JavaScript | MDN]{@link https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Array/includes#Polyfill}
   */
  Array.prototype.includes = function(searchElement /*, fromIndex*/) {
    'use strict';
    if (this == null) {
      throw new TypeError('Array.prototype.includes called on null or undefined');
    }

    var O = Object(this);
    var len = parseInt(O.length, 10) || 0;
    if (len === 0) {
      return false;
    }
    var n = parseInt(arguments[1], 10) || 0;
    var k;
    if (n >= 0) {
      k = n;
    } else {
      k = len + n;
      if (k < 0) {k = 0;}
    }
    var currentElement;
    while (k < len) {
      currentElement = O[k];
      if (searchElement === currentElement ||
         (searchElement !== searchElement && currentElement !== currentElement)) { // NaN !== NaN
        return true;
      }
      k++;
    }
    return false;
  };
}

if (!Array.from) {
  /**
   * Production steps of ECMA-262, Edition 6, 22.1.2.1
   * @see [Polyfill — Array.from() — JavaScript | MDN]{@link https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Array/from#Polyfill}
   */
  Array.from = (function () {
    var toStr = Object.prototype.toString;
    var isCallable = function (fn) {
      return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
    };
    var toInteger = function (value) {
      var number = Number(value);
      if (isNaN(number)) { return 0; }
      if (number === 0 || !isFinite(number)) { return number; }
      return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
    };
    var maxSafeInteger = Math.pow(2, 53) - 1;
    var toLength = function (value) {
      var len = toInteger(value);
      return Math.min(Math.max(len, 0), maxSafeInteger);
    };

    // The length property of the from method is 1.
    return function from(arrayLike/*, mapFn, thisArg */) {
      // 1. Let C be the this value.
      var C = this;

      // 2. Let items be ToObject(arrayLike).
      var items = Object(arrayLike);

      // 3. ReturnIfAbrupt(items).
      if (arrayLike == null) {
        throw new TypeError("Array.from requires an array-like object - not null or undefined");
      }

      // 4. If mapfn is undefined, then let mapping be false.
      var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
      var T;
      if (typeof mapFn !== 'undefined') {
        // 5. else
        // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
        if (!isCallable(mapFn)) {
          throw new TypeError('Array.from: when provided, the second argument must be a function');
        }

        // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
        if (arguments.length > 2) {
          T = arguments[2];
        }
      }

      // 10. Let lenValue be Get(items, "length").
      // 11. Let len be ToLength(lenValue).
      var len = toLength(items.length);

      // 13. If IsConstructor(C) is true, then
      // 13. a. Let A be the result of calling the [[Construct]] internal method 
      // of C with an argument list containing the single item len.
      // 14. a. Else, Let A be ArrayCreate(len).
      var A = isCallable(C) ? Object(new C(len)) : new Array(len);

      // 16. Let k be 0.
      var k = 0;
      // 17. Repeat, while k < len… (also steps a - h)
      var kValue;
      while (k < len) {
        kValue = items[k];
        if (mapFn) {
          A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
        } else {
          A[k] = kValue;
        }
        k += 1;
      }
      // 18. Let putStatus be Put(A, "length", len, true).
      A.length = len;
      // 20. Return A.
      return A;
    };
  }());
}

if (typeof Object.assign != 'function') {
  /**
   * This polyfill doesn't support symbol properties, since ES5 doesn't have symbols anyway:
   * @see [Polyfill — Object.assign() — JavaScript | MDN]{@link https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Object/assign#Polyfill}
   */
  Object.assign = function (target, varArgs) { // .length of function is 2
    'use strict';
    if (target == null) { // TypeError if undefined or null
      throw new TypeError('Cannot convert undefined or null to object');
    }

    var to = Object(target);

    for (var index = 1; index < arguments.length; index++) {
      var nextSource = arguments[index];

      if (nextSource != null) { // Skip over if undefined or null
        for (var nextKey in nextSource) {
          // Avoid bugs when hasOwnProperty is shadowed
          if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
            to[nextKey] = nextSource[nextKey];
          }
        }
      }
    }
    return to;
  };
}

let tokenList = document.createElement('div').classList;
tokenList.add('foo', 'bar');
if (!tokenList.contains('bar')) {
	let add = DOMTokenList.prototype.add;
	DOMTokenList.prototype.add = function () {
		Array.from(arguments).forEach(add, this);
	};
	let remove = DOMTokenList.prototype.remove;
	DOMTokenList.prototype.remove = function () {
		Array.from(arguments).forEach(remove, this);
	};
}

if (!('includes' in String.prototype)) {
	/**
	 * Determines whether one string may be found within another string, returning true or false as appropriate.
	 * @param {string} searchString - A string to be searched for within this string.
	 * @param {number} [position=0] - The position in this string at which to begin searching for searchString.
	 * @returns {boolean}
	 * @see [String.prototype.includes ( searchString [ , position ] ) | ECMAScript 2016 Language Specification]{@link https://www.ecma-international.org/ecma-262/7.0/index.html#sec-string.prototype.includes}
	 * @see [String.prototype.includes() — JavaScript | MDN]{@link https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/String/includes}
	 * @name String#includes
	 */
	Object.defineProperty(String.prototype, 'includes', {
		configurable: true,
		enumerable: false,
		writable: true,
		value: function (searchString, position) {
			return this.indexOf(searchString, position) !== -1;
		}
	});
}

let EventInit = {
	bubbles: false,
	cancelable: false,
	composed: false,
};

try {
	new Event('click');
} catch (e) {
	/**
	 * Returns a new event whose type attribute value is set to type.
	 * @constructor
	 * @param {string} type
	 * @param {EventInit} [eventInitDict] Allows for setting the bubbles and cancelable attributes via object members of the same name. 
	 * @see [Interface Event | Events | DOM Standard]{@link https://dom.spec.whatwg.org/#interface-event}
	 * @name Event
	 */
	Object.defineProperty(window, 'Event', {
		writable: true,
		enumerable: false,
		configurable: true,
		value: function (type) {
			let event = document.createEvent('Event');
			let eventInitDict = convertToDictionary(arguments[1], EventInit);
			event.initEvent(type, eventInitDict.bubbles, eventInitDict.cancelable);
			return event;
		},
	});
}

/**
 * dictionary型に変換します。
 * @param {?Object} obj
 * @param {*} defaultValues
 * @returns {Object}
 */
function convertToDictionary(obj, defaultValues) {
	var objIsObject = typeof obj === 'object' && obj !== null;
	if (obj !== undefined && obj !== null && !objIsObject) {
		throw new TypeError((typeof obj === 'string' ? '"' + obj + '"' : obj) + ' can not be converted to a dictionary');
	} else if (objIsObject) {
		if (obj.constructor === Date) {
			throw new TypeError('"' + obj + '" can not be converted to a dictionary');
		}
	} else {
		obj = {};
	}
	for (var key in defaultValues) {
		if (key in obj) {
			defaultValues[key] = obj[key];
		}
	}
	return defaultValues;
}

let ClipboardEventInit = Object.assign({}, EventInit, {
	clipboardData: null,
});

if (typeof ClipboardEvent === 'undefined') {
	let clipboardDataList = new WeakMap();
	
	/**
	 * Returns a new event whose type attribute value is set to type.
	 * @constructor
	 * @param {string} type
	 * @param {ClipboardEventInit} [eventInitDict] Allows for setting the bubbles, cancelable, and clipboardData attributes via object members of the same name. 
	 * @see [DOM Standard]{@link https://www.w3.org/TR/clipboard-apis/#clipboardevent}
	 * @name ClipboardEvent
	 */
	Object.defineProperty(window, 'ClipboardEvent', {
		writable: true,
		enumerable: false,
		configurable: true,
		value: function (type) {
			let event = new Event(type, arguments[1]);
			clipboardDataList.set(event, convertToDictionary(arguments[1], ClipboardEventInit).clipboardData);
			Object.defineProperty(event, 'clipboardData', {
				enumerable: true,
				configurable: true,
				get: function () {
					return clipboardDataList.get(event);
				},
			});
			return event;
		},
	});
	ClipboardEvent.prototype = Object.create(Event.prototype);
	
	document.execCommand = function (command) {
		if (command === 'copy') {
			document.dispatchEvent(new ClipboardEvent('copy', {
				bubbles: true,
				cancelable: true,
				clipboardData: window.clipboardData,
			}));
		} else {
			Document.prototype.execCommand.apply(document, [command]);
		}
	};
}

try {
	new URL('http://example.com/');
} catch (exception) {
	let urlList = new WeakMap();
	let queryObjectList = new WeakMap();
	
	/**
	 * To parse a URL without using a base URL, invoke the constructor with a single argument.
	 * If you rather resolve it against the base URL of a document, use baseURI.
	 * @constructor
	 * @param {string} url
	 * @param {(URL|string)} [base="about:blank"]
	 * @see {@link http://url.spec.whatwg.org/#constructors 7.1 Constructors - URL Standard}
	 */
	URL = Object.assign(function (url) {
		var base = arguments[1];
		if (!(0 in arguments)) {
			throw new TypeError('Not enough arguments');
		}
		urlList.set(this, document.implementation.createHTMLDocument('').createElement('a'));
		urlList.get(this).href = url;
		if (base) {
			if (!(base instanceof URL)) {
				base = new URL(base);
			}
			base.hash = '';
			['protocol', 'username', 'password', 'host', 'pathname', 'search'].forEach(function (propertyName) {
				let property = base[propertyName];
				if (property) {
					urlList.get(this)[propertyName] = property;
				}
			});
		} else {
			if (urlList.get(this).protocol === ':') {
				throw new TypeError('An invalid or illegal string was specified');
			}
		}
	}, URL);
	let properties = {};
	['href', 'origin', 'protocol', 'username', 'password', 'host', 'hostname', 'port', 'pathname', 'search', 'hash']
		.forEach(function (propertyName) {
			properties[propertyName] = {
				enumerable: true,
				configurable: true,
				get: function () { return urlList.get(this)[propertyName]; },
				set: function (value) { urlList.get(this)[propertyName] = value; },
			};
		});
	Object.defineProperties(URL.prototype, properties);
	URL.prototype.toString = function () {
		return urlList.get(this).toString();
	};
}

})();
