Google Authenticator in Symfony 5

  - Symfony 5.*
  -	Login and Registration 
  -	 Authenticated User Dashboard
  -	 User Settings Page for turning on/off/reconfiguring two factor authentication.
  -	 Two factor authentication / Symfony Google Authenticator
  -	 Password Reset 
  -	 TOS Acceptance

Configuration and setup

  1)	Composer update.
  2)	Update .env file for database, email and recaptcha set up 
  -	MAILER_DSN
  -	GOOGLE_RECAPTCHA_SITE_KEY=
  -	GOOGLE_RECAPTCHA_SECRET= 
  -	DATABASE_URL=
  2)	Run command for database creation : php bin/console doctrine:database:create
  3)	Run command for tables creation : php bin/console doctrine:schema:create

