# MG Whitescreen Plugin Checker
The "Whitescreen of Death" can be caused for many many reasons. One of them could be related to plugins issues. Maybe you updated or change some code in some plugins and suddenly everything stopped workings. Also you can inherit a Website with tons of plugin and it's such a waste of time to activate/deactivate every single one of them just to verify which one is broken.

**This plugin is your solution!**
Download it, install it and check which plugin is giving a hard day **without interfering with the traffic of your website** (it only activates or deactivates plugins with HTTP calls that has some specific GET parameters).

Checkout the feautures:
- Checks the status of your website activating and deactivating automatically every plugin without interfering with the rest of the traffic.
- Checks the status of your website through a complete combination of all the plugins to verify if the problem is the result of 2 or more plugins.
- Allows you to see the debug information in case of error.
 
## Installation
1. Download the `mg-whitescreen-plugin-checker.php` file.
2. Upload it using FTP in the folder `your-wordpress/wp-content/mu-plugins` of your website.

## Syntax
### Test all the plugins individually
Go to the URL with the "Whitescreen of Death" and add this `GET` paramter: `?wpc=individually`
e.g. http://mysite.com/contact?wpc=individually

This will run a test activating and deactivating all the plugins of the website. If the [HTTP Code Response is not 200](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#2xx_Success) it will prompt an error message with the name of the specific plugin.

### Test all the combinations of plugins
There are some cases where the Whitescreen appears because of a combination of uncomptaible plugins. This functions will create all the possible combination and checks the status of your website.
**I suggest to use this method after checking the "individually" one**. It could take longer due to the amount of possible combinations.
In case of any error, the system will prompt the name of the combination of plugins and the HTTP code.

Add to the URL this `GET` parameter: `?wpc=complete`
e.g. http://mysite.com/contact?wpc=complete