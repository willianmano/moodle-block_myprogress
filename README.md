# Moodle My Progress Block #

This block displays the user completion progress in a course.

It is also possible to enable/disable the course, group, and cohort progression average. So, students can compare their performance with the others.

## Sponsor special thanks

This plugin was kindly sponsored by **Luiggi Sansonetti** from [e-Learning, Conseils & Solutions](http://www.luiggisansonetti.fr/conseils).

Thank you very much for supporting Moodle community and the open-source initiative.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/blocks/myprogress

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.
