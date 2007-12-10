/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Web:Extend namespaces */

window.wee	= {
	fixes:		{},
	widgets:	{
		multiplefileinput:	{}
	}
};

/* Boot: Fixes functions */

wee.fixes.inlineblock = function() {
	if ($.browser.mozilla) {
		$('form.block').find('li>label').each(function(i) {
			var w = document.defaultView.getComputedStyle(this,'').getPropertyValue('width');
			$(this).html('<span style="display:block;width:' + w + '">' + this.innerHTML + '</span>');
			$(this).css('display', '-moz-inline-box');
		});
	}
}

/* Boot: Widgets */

wee.widgets.multiplefileinput.change = function(e) {
	var t = e.target;
	$(t.parentNode).after('<li><input type="file" name="' + t.name + '" title="' + t.title + '" accept="' + t.accept + '"/></li>');
	$(t).unbind('change');
	$(t.parentNode.nextSibling.firstChild).bind('change', wee.widgets.multiplefileinput.change);
}

/* Boot */

$(document).ready(function() {
	$('fieldset[class="multiplefileinput"] input').bind('change', wee.widgets.multiplefileinput.change);
});
