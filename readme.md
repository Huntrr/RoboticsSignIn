# Robotics Sign In Page

## Set up

More or less straightforward, drop-in and go. All you need is a MySQL database,
an Apache server with PHP, and then you need to make the following basic
changes to some .php files:

1. In db.php, set up $USERNAME, $PASSWORD, and $HOST to reflect the MySQL setup
1. In login.php, change $password and $adminpassword to anything you'd like
1. Because of a weird setup (? me being lazy and not knowing and php?) you might need to host this at http://YOURDOMAIN.com/robits/(index.php). Sorry.
