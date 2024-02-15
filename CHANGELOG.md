# Changelog

### 3.4.11

* Fix previously broken unit tests
* Add github-actions
* Format code by following PSR-12

### 3.4.10

* throw BpostInvalidXmlResponseException if XML response if not a valid XML

### 3.4.9

* Geo6.php supports Country
* Fix string/int comparison in ApiCaller
Add PHP7.1 -> 8.0 to travis CI job

### 3.4.8

* Update version of some composer packages

### 3.4.7

* Endpoint change for Geo6/Pudo

### 3.4.6

* For National, option Insured must call class Insurance
* Fixed the parcel locker 'unregistered', UnregisteredParceLockerMember is deprecated #16

### 3.4.5

* Fix PHP signatures

### 3.4.1

* Refactoring
* Fix issues

### 3.4.0

* Add retro-compatibility with tijsverkoyen library (namespace changes)
* Complete the README (examples, broken links, ...)
* Change API URL (api.bpost.be -> api-parcel.bpost.be)
* Labels features
  * Possibility to append field "order reference"
  * Possibility to force printing
* Geo6 features
  * Geo6 is now called via HTTPS
  * Send data to API via POST
  * Add Geo6::getPointType() to calculate point types
* Products features
  * Add "bpack World Easy Return" to international products
  * Box At247 can contain a product bpack 24/7

### 3.3.0

* Use bpost API version 3.3 (yet, bpack part only)
* Change namespace TijsVerkoyen\Bpost to Bpost\BpostApiClient
* Add more unit tests to perform code coverage
* Begin to based the unit tests on XML examples [given by bpost](http://bpost.freshdesk.com/support/solutions/articles/4000037653-where-can-i-find-the-bpack-integration-manual-examples-and-xsd-s-)
* Add CONTRIBUTING.md

### 3.0.1

* Allowed SaturdayDelivery, see https://github.com/tijsverkoyen/bpost/pull/11

### 3.0.0

* Bugfix: removed usage of undefined constant, see https://github.com/tijsverkoyen/bpost/pull/8


### 1.0.1

* Made the classes compliant with PSR
* Using Namespaces
* From now on we will follow the versionnumbers that bpost is using, so we will
  skip a major version
* Introduction of the GEO-services
* Introduction of the Bpack24/7-services
* Composer support
* Decent objects
