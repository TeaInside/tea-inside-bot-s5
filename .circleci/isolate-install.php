<?php


print shell_exec("
	cd /tmp &&
	git clone https://github.com/ioi/isolate &&
	cd isolate &&
	make -j3; make install
	2>&1
");
