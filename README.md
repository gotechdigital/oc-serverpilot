Awebsome ServerPilot
==================
This is an amazing plugin for manage your  OctoberCMS apps with [ServerPilot.io](https://www.serverpilot.io/?refcode=5e177d453154) &  [DigitalOcean](https://m.do.co/c/08c1ea53e42e) 

It is a difficult find the secure engine for your apps, therefore saves time with ServerPilot, they makes security configuration of your [Droplet/VPS](https://m.do.co/c/08c1ea53e42e) by you.

With Awebsome.ServerPilot you can make this:

## Resources & Functions

##### Servers
+ Server Listing
+ Firewall / Updates Status
+ Sync data from ServerPilot.io to October

##### System Users
+ Add SSH/FTP Users
+ Reset Password

##### Databases
+ Create Database
+ Reset Password
+ Delete Database

##### Apps
+ Apps Listing
+ Create App
+ Manage Runtime (PHP Version)
+ Manage Domains
+ SSL Status
+ Delete an App


FTP Configs
==================
To generate a password for the first time, reset the password from "Users", then go to "App", delete you sftpconfig and save to generate again. 

Example FTP Config of Atom
    
    # You can modify and save your custom settings
    {
        "protocol": "sftp",
        "host": "ourdomain.com",
        "port": 22,
        "user": "apps",
        "pass": "reset the password",
        "promptForPass": false,
        "remote": "/srv/users/{user}/apps\{app}/public",
        "agent": "",
        "privatekey": "",
        "passphrase": "",
        "hosthash": "",
        "ignorehost": true,
        "connTimeout": 30000,
        "keepalive": 10000,
        "keyboardInteractive": false,
        "watch": [],
        "watchTimeout": 500
    }
    
    
We recommend [ServerPilot.io](https://www.serverpilot.io/?refcode=5e177d453154) &  [DigitalOcean](https://m.do.co/c/08c1ea53e42e)
==
