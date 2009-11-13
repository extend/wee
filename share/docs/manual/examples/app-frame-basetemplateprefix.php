<?php

class myBaseFrame extends weeFrame {
	protected $sBaseTemplatePrefix = 'admin/';
}

class myAdminIndexFrame extends myBaseFrame {
	protected $sBaseTemplate = 'index'; // will use the app/tpl/admin/index.tpl file
}

class myAdminUsersFrame extends myBaseFrame {
	protected $sBaseTemplate = 'users'; // will use the app/tpl/admin/users.tpl file
}
