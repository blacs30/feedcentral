# Feed Central

**The usage of this app in production is highly discouraged because this will allow everyone to access all feeds of all users**

**Want a feature? Found a bug? Want to work on this? Fork it! Enhancement and bug requests will be closed immediately!**

This is merely a prototype to learn how to write a News app serverside plugin.

# Installation
Place this app in **owncloud/apps/**

# API
* **Method**: GET
* **URL**: /index.php/apps/feedcentral/rss
* **userId**: The login name of a user
* **id (optional)**: The id of the feed or folder if needed, defaults to 0
* **type (optional)**: The type of the feed, defaults to 3. Can be any of the following:
 * **0**: Only items of a feed, requires the id parameter
 * **1**: Only items of a folder, requires the id parameter
 * **2**: Only starred items
 * **3**: All items

## Examples
Get all starred itmes of the user with the id john:

**GET /index.php/apps/feedcentral/rss?userId=john&id=0&type=2**: