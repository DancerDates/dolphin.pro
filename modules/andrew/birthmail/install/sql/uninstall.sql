-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 't_birthmail_template';

-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `name` = 'birthmail';
