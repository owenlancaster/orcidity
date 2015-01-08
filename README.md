### **** ImpactStory API has gone commercial so the demo no longer works ****

### ORCIDity prototype developed during ORCID Hackathon, Oxford (May 23rd 2013)

This is a first version of the ORCIDity timeline mashup. Users can enter their ORCID iD and are presented with a timeline of their publications, with visual links to the source journal. Additional information including citation data (from ImpactStory.org) is also presented for each publication. Further development ideas and collaborations are most welcome.

### Live Demo

[Live Demo](http://143.210.56.154/orcidity)

### API Components
* ORCID public API (retrieving users publications)
* CrossRef API (for lookup of publication details)
* ImpactStory API (fetch citation information)

### Other Components
* Timeline JS (http://timeline.verite.co/)
* Codeigniter
* Twitter Bootstrap
* jQuery

### Install

You will need to set "AllowOverride All" in your apache config in order to allow access the .htaccess file (and possibly adjust the RewriteBase in the .htaccess file to suit your web directory).

To enable ImpactStory citations enter your API key in the application/config/orcidity.php file.

MySQL database may need to be created (and the connection settings entered in application/config/database.php). A dump of the database structure is available in resources/sql/orcidity.sql.
