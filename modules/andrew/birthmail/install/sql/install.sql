-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('t_birthmail_template', 'Happy Birthday <username>!', '<bx_include_auto:_email_header.html />\r\n<p><b>Dear <username></b>,</p><br /><p><b>Many congratulations on your birthday and have a great day !!</b></p>\r\n<bx_include_auto:_email_footer.html />', 'Cron birthday mail template.', 0);

-- cron jobs
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('birthmail', '0 0 * * *', 'ABMailCron', 'modules/andrew/birthmail/classes/ABMailCron.php', '');
