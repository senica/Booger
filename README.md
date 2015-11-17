Booger is a full Content Management System.

It was created by a web developer (me) for web developers. When it was created, I was doing a lot of heavy Wordpress. There was some nuances in Wordpress that just irked me and I've tried to address some of them in this CMS. While this is very young in develpment, and I've stopped developing on it for the time being, I would love for someone to pick it up and play around with it.

There is not a lot of documentation, but I'd be happy to answer any questions if you send me a message or post a ticket.

It is written in PHP with MySQL. I've moved to NodeJS mostly now, but PHP definitely still has it's place. I definitely miss the simplicity of setting up a PHP website.

I'm not sure if Booger is dead for me or not. I recently installed it to grab some screenshots for a portfolio, and got excited about it again. There are definitely some good ideas and merit in the project. The direction of it definitely had some fortitude. I think if some other people rallied around it, I'd jump back in. The road on these projects is very lonely sometimes :). For me, one of the discouraging factors, is there would definitely need to be some updates made as things have changed a bit in the past 5 years with web dev.

Couple small notes I'd like to make regarding the setup:

Modify assets/config.php and fill our the database settings. Note that if you use the install, make sure the secret in the gui matches the secret in the config.php before installing.


Set allow url fopen to on in your php.ini
allow_url_fopen = On
