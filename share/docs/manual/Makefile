#	
#	Web:Extend
#	Copyright (c) 2006-2010 Dev:Extend
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

all: clean
	-mkdir build
	-mkdir build/dia
# The behavior of 'dia -t' changed in 0.97.
# It now uses an -O OUTPUT parameter to define the output directory.
# Since we want to support both behaviors at the moment, we can't use
# that parameter, so we instead try to copy from both expected output directories.
	dia -t png dia/*
	-mv *.png build/dia/
	-mv dia/*.png build/dia/
# Proceed normally past this point.
	-mkdir build/snap
	cp snap/* build/snap/
	-mkdir examples/build
	source-highlight --output-dir=examples/build -f docbook examples/*.xml
	source-highlight --output-dir=examples/build -f docbook examples/*.html
	source-highlight --output-dir=examples/build -f docbook examples/*.js
	source-highlight --output-dir=examples/build -f docbook examples/*.php
	source-highlight --output-dir=examples/build -f docbook examples/*.sql
	source-highlight --output-dir=examples/build -f docbook examples/*.sh
	source-highlight --output-dir=examples/build -f docbook -s properties examples/*.cnf
	xmlto -o build/ xhtml toppage.xml

clean:
	-rm -rf examples/build

fclean: clean
	-rm -rf build
