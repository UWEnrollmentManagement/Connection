
UWDOEM/Connection
=============

Helper library for connecting to the university's x.509 secured web services.


Troubleshooting
===============

This library *will* throw warnings and exceptions when it recognizes an error. Turn on error reporting to see these. The following conditions will halt execution:

cURL Error Code 77
------------------

**Problem**: cURL cannot find the UWCA root certificate to verify the identify of the PWS/SWS servers.

**Solution**: Download the [.crt root CA bundle](http://curl.haxx.se/docs/caextract.html) to your server, ensure that your web-server process has read access to this bundle, and uncomment/edit the `curl.cainfo` line in your *php.ini* to reflect the location of this bundle.

cURL Error Code 58
------------------

**Problem**: cURL is having a problem using your private key.

**Solution**: You may have provided an incorrect private key password to `::createConnection`. If your private key requires a password, provide one, and ensure that it is correct.

No such file found for SSL key/certificate
------------------------------------------

**Problem**: Connection cannot find the key and/or certificate at the path you provided to `::createConnection`.

**Solution**: Ensure that you provided the correct path to these files and that your web-server process has read-access to these files.

Script execution halts/no output
----------------------

**Problem**: This might be caused by an internal error in cURL while accessing your private key/certificate which causes PHP to die unexpectedly.

**Solution**: I was able to solve this by setting permissions on my key/certificate to read only. Specifically, I turned off write access for all parties.


Compatibility
=============

* PHP 5.5, 5.6, 7.0

Todo
====

See GitHub [issue tracker](https://github.com/UWEnrollmentManagement/Connection/issues/).


Getting Involved
================

Feel free to open pull requests or issues. [GitHub](https://github.com/UWEnrollmentManagement/Connection) is the canonical location of this project.

Here's the general sequence of events for code contribution:

1. Open an issue in the [issue tracker](https://github.com/UWEnrollmentManagement/Connection/issues/).
2. In any order:
  * Submit a pull request with a **failing** test that demonstrates the issue/feature.
  * Get acknowledgement/concurrence.
3. Revise your pull request to pass the test in (2). Include documentation, if appropriate.
