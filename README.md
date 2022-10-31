# Chrissyx Homepage Scripts - Counter

[![version](https://img.shields.io/badge/version-3.2.0-blue)](https://www.chrissyx.com/scripts.php#Counter)

## Introduction
More than a simple counter, this one has additional features and a multilingual administration panel. Starting there everything is configurable, from the type of output up to storage locations of the internal system files; cached settings are renewed automatically and immediately active after logout. The optional backup possibility can be set to send the current value by email after each certain amount of hits. By using the optional IP blocker the script is operating as a true visitor counter or just as a simple hit counter.

## Requirements
* ![php](https://img.shields.io/badge/php-%3E%3D5.3-blue)
* ![webspace](https://img.shields.io/badge/webspace-chmod--able-lightgrey)

## Installation
1. Upload in that directory, in which your website is (and you're planning to use the counter), the folder `chscore` including its contents. If it already exists, overwrite the files.
2. If not yet present, paste in this code at the very beginning of your site (even before `<html>`, `<!DOCTYPE [...]` or `<?xml [...]`):  
   `<?php include('chscore/CHSCore.php'); ?>`
3. Point your browser to your website and attach `?module=CHSCounterAdmin` to the address. Follow the instructions.

### Example
* Your site is:  
  `https://www.mySite.com/myFolder/myIndex.php`  
  <sub>(Put in here in the first line `<?php include('chscore/CHSCore.php'); ?>`)</sub>
* Then upload the counter to:  
  `https://www.mySite.com/myFolder/chscore/`
* And start installation with:  
  `https://www.mySite.com/myFolder/myIndex.php?module=CHSCounterAdmin`

## Update
### Update from 3.1 to 3.2
Upload the folder `chscore` including its contents and replace each existing file.

### Update from 3.0 to 3.2
Back up the files `counter.dat` and, if present, `ip.dat` from folder `chscore/modules/CHSCounter/`. Delete the folder `chscore` afterwards and install the new version as explained above. Pasted in code on your website doesn't need to be changed again. Finally upload the backed up files to the `chscore/data/` folder and replace the existing ones.

## FAQ
* How to manage my counter?  
  Just point your browser to your website by adding `?module=CHSCounterAdmin` to the address, as you did during the installation and follow the instructions.
* I've forgot my password!  
  Go to the login form, you can request a new password there. The old one is still valid until you log in with the new password.
* Can I use other images for displaying the counter value?  
  Sure, you just need to replace the provided PNG images with your own ones. For each number an own image, that means the `0` has to be named `0.png`, the `1` has to be named `1.png`, etc. Put all images with numbers into the `chscore/images/CHSCounter/` folder.
* I'm getting a message "ERROR: Can't create config file!"?!?  
  Set with your FTP program and chmod command the permisson to `755` for these folders:
  * `chscore/config/`
  * `chscore/data/`
* Is it possible to translate the counter to another language?  
  Of course, copy an appropriate INI file (e.g. `en-US.CHSCounter.ini`) from the `chscore/languages/` folder and rename the official language code of the filename to the corresponding of the desired language. Like `fr-FR.CHSCounter.ini` for French in France or `nl.CHSCounter.ini` for general Dutch. Start translating the strings between the quotation marks and check the hints at the beginning of the file. By having a complete translation, upload it to the `chscore/languages/` folder and if applicable choose it from the language menu in the administration panel. Please send it also to me for providing it to other user! :slightly_smiling_face:
* My question isn't answered here!  
  Please visit my board at https://www.chrissyx.com/forum/ or write me an email: chris@chrissyx.com

## Credits
© 2004-2022 by Chrissyx  
Powered by V4 LeetCore Technology  
https://www.chrissyx.de/  
https://www.chrissyx.com/  
[![Twitter Follow](https://img.shields.io/twitter/follow/CXHomepage?style=social)](https://twitter.com/intent/follow?screen_name=CXHomepage)