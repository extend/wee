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
	Call a function every X milliseconds; unless task is locked.

	Provides some form of reliable async when performing tasks.
	The task repeats itself every X seconds but only if it is unlocked.
	It first begins to lock itself, and then only unlocks when the
	task is completed. This can be directly after the end of the function
	or after an asynchronous call. This is especially useful in that
	last case, because it ensures you don't make 2 same asynchronous
	calls at the same time.

	@param taskFn The task function to call every interval.
	@param interval The interval, in milliseconds.
*/

task = function(taskFn, interval) {
	this.version = '0.1';

	this.fn = taskFn;
	this.locked = false;

	// run the thread every intervals
	var self = this;
	this.id = window.setInterval(function(){self.run();}, interval);
}

/**
	Run the task even if the interval isn't elapsed, unless it is locked.
*/

task.prototype.run = function() {
	if (!this.locked) {
		this.locked = true;
		if (this.fn(self))
			this.locked = false;
	}
}
