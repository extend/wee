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

all: lint api test
	@@echo "Web:Extend build complete."

lint:
	@@for file in `find . -type f -name "*.php"`; do php -l $$file; done

svnlint:
	@@svn stat | grep 'php' | awk '{print "php -l " $$2}' | sh

api: tools/api/api.xml
	@@php tools/api/makeapi.php tools/api/

tools/api/api.xml:

test:
	@@php tools/tests/maketests.php tools/tests/

clean:
	@@-rm -rf tools/api/api.xml

todo:
	@@for file in `find . -name "*.php" -o -name "*.tpl" -o -name "*.form" -type f`; \
		do grep -H -i TODO $$file; \
	done
