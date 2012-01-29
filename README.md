A simple way to mirror your videos from Youtube to Vimeo.  Designed by someone who doesn't trust a single point of failure for his data.

#IT DOES WUT?
This tool copies all of the videos from your Youtube account to a Vimeo account.

#LIMITATIONS
Vimeos API places a 15GB per week limit, we have to adhere to that.

#PIRATES!
Do not use this tool for replicating copywritten material.  This tool is designed so users can distribute their content safely. 

#GOOGLE STAFF
I know, I know..  This script isn't very "pro Youtube" but before you send me yet another email complaining please research my commitments to developing the open-web and online interopability.

#GETTING STARTED
* [Register for a Vimeo consumer and secret key at Vimeo.com](http://vimeo.com/api/applications/new)..   Wait for your keys..
* [Download this Tool](https://github.com/johnyma22/Transfer-Video-from-Youtube-to-Vimeo/tarball/master).  
* Extract the tar.gz file to a moist place
* Open up config.php
* Insert your vimeo keys and youtube username.
* Save and Close config.php
* Test by running index.php (php index.php)
* Once you are happy, change debug to false in config.php and add a cron job if you like

#PRE-REQS
* Php5+
* Curl
* A belief in Santa and Unicorns

#TODO
* Automate the upload so it doesn't require any user input.  Currently Oauth is handled inside of the browser and this sucks.
* Ensure all ID3 data is properly copied over.
* Scrap all CSS and mark up, replacing it w/ nice STDOut outputs..  
* Ensure it runs pleasantly as a cron job.
* Ensure debug mode does everything it sais it will.
* Ensure all metadata is encoded
* Provide an "On successful upload" callback function for doing stuff like writing to databases.
