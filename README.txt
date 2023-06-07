                                 .-..-.
   _____                         | || |
  /____/-.---_  .---.  .---.  .-.| || | .---.
  | |  _   _  |/  _  \/  _  \/  _  || |/  __ \
  * | | | | | || |_| || |_| || |_| || || |___/
    |_| |_| |_|\_____/\_____/\_____||_|\_____)

Moodle - the world's open source learning platform

Moodle <https://moodle.org> is a learning platform designed to provide
educators, administrators and learners with a single robust, secure and
integrated system to create personalised learning environments.

You can download Moodle <https://download.moodle.org> and run it on your own
web server, ask one of our Moodle Partners <https://moodle.com/partners/> to
assist you, or have a MoodleCloud site <https://moodle.com/cloud/> set up for
you.

Moodle is widely used around the world by universities, schools, companies and
all manner of organisations and individuals.

Moodle is provided freely as open source software, under the GNU General Public
License <https://docs.moodle.org/dev/License>.

Moodle is written in PHP and JavaScript and uses an SQL database for storing
the data.

See <https://docs.moodle.org> for details of Moodle's many features.

# Plugins that Sundsvalls kommun has developed

## List of proprietary plugins
- Admin settings block ( /block/block_adminsettingblock )
The block is used for the administrator to have easy access to being able to show/hide courses.
The plugin is dependent on the Course list extension.

- Registering via väntelista ( /enrol/enrol_waitlistext )
Waiting list that controls who gets the opportunity to register for the course.
The plugin depends on enrol_waitlist.

- Course list extension ( /theme/theme_courseextension )
The theme makes it possible to control what to see in the course list before clicking on the respective course.
Proprietary theme based on boost.

- authupdateevent ( /local/local_authupdateevent )
Updates database with login method SSO for users created overnight.

- Badgeevent ( /local/local_badgeevent )
Sends a reminder 1 month before if a brand expires.
Sends an email if the badge you received is required for a course.
