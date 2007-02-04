#	
#	Web:Extend
#	Copyright (c) 2006 Dev:Extend
#
#	This library is free software; you can redistribute it and/or
#	modify it under the terms of the GNU Lesser General Public
#	License as published by the Free Software Foundation; either
#	version 2.1 of the License, or (at your option) any later version.
#
#	This library is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
#	Lesser General Public License for more details.
#
#	You should have received a copy of the GNU Lesser General Public
#	License along with this library; if not, write to the Free Software
#	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
#

all: lint docs test
	@@echo "Web:Extend build complete."

lint:
	for file in `find . -type f -name "*.php"`; do php -l $$file; done

docs: docs/api.xml
	php docs/makeapi.php docs/

docs/api.xml:

test:
	php tests/maketests.php tests/

clean:
	-rm -rf docs/api.xml

todo:
	grep -r --include=*.php -i TODO ./

new:
	mkdir form include locale skins tpl

