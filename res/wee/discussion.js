/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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

/**
	Generic discussion mechanism, handling both automatic refreshing
	and async form submission. Both refresh and submit operations are
	protected by locks, preventing two running at the same time.
	Best used with Taconite as it allows page modifications without
	any additional JS code other than initializing this class.

	@param data.formSelector Selector to the form used for async submit.
	@param data.refreshURL Refresh URL that will be used to refresh the page async.
	@param data.refreshInterval Interval between each page refresh.
	@param data.beforeSubmit Passed to the $.ajaxSubmit method.
	@param data.afterSubmit Passed to the $.ajaxSubmit method.
	@param data.errorSubmit Passed to the $.ajaxSubmit method.
*/

discussion = function(data) {
	this.version = '0.1';

	this.lastId = 0;
	this.submitLocked = false;

	var form = $(data.formSelector);
	var self = this;

	// start refreshing
	this.refreshTask = new task(function() {
		$.ajax({
			url: data.refreshURL,
			data: 'lastid=' + self.lastId,
			timeout: data.refreshInterval,
			error: function() { self.refreshTask.locked = false; }
		});
		return false;
	}, data.refreshInterval);

	// bind submit event
	form.submit(function() {
		if (self.submitLocked)
			return false;
		self.submitLocked = true;

		$(this).ajaxSubmit({
			timeout: data.refreshInterval,
			beforeSubmit: data.beforeSubmit,
			error: data.errorSubmit,
			success: function() {
				self.refreshTask.run();
				self.submitLocked = false;
				if (data.afterSubmit)
					data.afterSubmit();
			}
		});

		return false;
	});
}
