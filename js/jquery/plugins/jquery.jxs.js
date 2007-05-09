// USE $Rev: 160 $ or higher.
/***
 * jXs		: http://www.brainknot.com/code/jXs.js
 * help 		: http://www.brainknot.com/code/jxs.htm
 * jQuery	: http://www.jquery.com
 *
 * @see Ryan Asleson, Nathaniel T. Schutta Taconite framework at http://taconite.sourceforge.net/
 * @see Mike Alsup original jQuery plug-in at http://www.malsup.com/jquery/
 * @author Kenton Simpson (kenton.simpson@gmail.com)
 * @author Mike Alsup (malsup@gmail.com)
 * @updated 09/13/2006 5:01AM
 * @ver 1.2.1.1
 */

 /**
  * jXs XML parser
  * @param object XML document
  * @param object collection of functions [optional]
  * @return void
  */
jQuery.jXs = function(xmlDoc, handlers) {
	if(!xmlDoc.nodeType) {
		throw({
			message: "[JXS_PARSER]: This is not a vaild jXs Script",
			command: {},
			script: xmlDoc
		});
	}

	var self = arguments.callee;
	jQuery.extend(self.handlers, handlers || {});
	jQuery("/root/*", xmlDoc).each(function() {
		var htmNodes = [];
		if(this.childNodes.length) {
			jQuery(">", this).each(function() {
				var temp = self.make(this);
				htmNodes.push(temp);
			});
		}

		if (typeof self.handlers[this.tagName] == "function") {
			self.handlers[this.tagName](this, htmNodes);
		} else if(typeof jQuery.fn[this.tagName] == "function") {
			jQuery(this.getAttribute("select"))[this.tagName](htmNodes.length ? htmNodes : undefined);
		} else if (typeof jQuery[this.tagName] == "function") {
			jQuery[this.tagName](htmNodes);
		} else {
			throw({
				message: "[JXS_PARSER]: Tag "+ this.tagName +" is not defined",
				command: this,
				script: xmlDoc
			});
		}
	});
};

/* **
 * jXs DOM controller for Michael Geary jQuery DOM builder plug-in
 * @see http://mg.to/topics/programming/javascript/jquery
 * @private
 * @name make
 * @type jQuery.jXs
 * @param XML template node
 * @return string javascript code to be evaled
 */
jQuery.jXs.make = function(xmlNode) {
	var self = arguments.callee;
	switch(xmlNode.nodeType) {
		case 1:  // element
			return element(xmlNode);
		case 3:  // text
		case 4:  // cdata -- Corrected by Mike Alsup 07/26/2006
			var t = xmlNode.nodeValue.replace(/^\s+|\s+$/g, " "); // condense white space
			return t.length < 1 ? undefined : document.createTextNode(t);
		default:
			return undefined; // do nothing;
	}
	function element(xNode) {
		var node = document.createElement(xNode.tagName);
		var isradio = false;
		var attr = [];
		for(var i = 0, a = xNode.attributes; i < a.length; i++) {
			jQuery(node).attr(a[i].nodeName, a[i].nodeValue);
			attr.push(a[i].nodeName + '="' + a[i].nodeValue + '"');
		}
		for(var i = 0, c = xNode.childNodes; i < c.length; i++) {
			var child = self(c[i]);
			if(child) {
				node.appendChild(child);
			}
		}

		if(jQuery.browser.msie && node.type == "radio") {
			return document.createElement("<input "+ attr.join(" ") +" />");
		}

		return node;
	}
};

/***
 * Methods to overide or control jQuery methods
 * @private
 */
jQuery.jXs.handlers = {
	attribute: function(element) {
		var a = element.attributes;
		for(var i = 0; i < a.length; i++) {
			if(a[i].nodeName == "select") continue;
			jQuery(element.getAttribute("select")).attr(a[i].nodeName, a[i].nodeValue);
		}
	},
	script: function(element, nodes) {
		var jscode = element.firstChild.nodeValue;
		jQuery.script(jscode);
	},
	replacein: function(element, nodes) {
		jQuery(element.getAttribute("select")).empty().append(nodes);
	}
};


// examples of custom tags, you can moves these to another js file or delete.
jQuery.script = function(code) {
	eval(code);
};

jQuery.include = function(a) {
	for(var i = 0; i <  a.length; i++) {
		document.getElementsByTagName("head")[0].appendChild(a[i]);
	}
};

jQuery.fn.replace = function(a) {
	return this.after(a).remove();
};

jQuery.fn.replacein = function(a) {
	return this.empty().append(a);
};

jQuery.fn.displace = function(a) {
	return this.empty().append(a);
}
