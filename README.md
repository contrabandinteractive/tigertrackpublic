# tigertrackpublic

The "web" folder contains the frontend and a PHP script that grabs location data. You must have a Google Maps API Key and a MySQL database to use.

The "azure iot scripts" folder contains the code that reads the collars, as well as code for a simulated device. For demo purposes, it is configured to send random location data from within a 10km radius of Kathmandu, and posts the longitude and latitude, along with ID number, to a server which stores it in the MySQL database. The production version will use real data.
