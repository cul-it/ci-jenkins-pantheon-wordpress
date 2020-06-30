# Ares for WordPress

The Ares plugin for Wordpress displays course reserves data from locally maintained Web Services that query the Ares reserves system. The code is based on a Drupal module created in May 2010 by Adam Smith which was in turn based on an interface approach used at Mann Library.


## Usage

Upon activating the plugin, a page is created with the name "Reserves" at the URL: ```/reserves```. This page contains a shortcode for invoking the full Ares interface, and obviously the shortcode can be used on other pages to either display the full interface or just the select list of courses whose items will then be displayed on ```/reserves```.

At is simplest, you can simply display a select box of all courses that will then display results on ```/reserves```:

```
[cu_ares_interface]
```

You can specify whether or not the select box goes to ```/reserves``` (the default is ```style="page"```) or simply displays results inline, below the form:

```
[cu_ares_interface style="inline"]

```
Obviously, the inline style is what is used on the ```/reserves``` page itself.

Finally, you can limit the courses shown to those associated with a particular library (the default is ```library="all"```):

```
[cu_ares_interface library="music" style="inline"]
```

See the "Web Services Description" section below for a reference to the list of available library codes.

The plugin also uses a jQuery plugin: http://tablesorter.com.


## Performance

This plugin requests JSON formatted output from the Web Services for use by jQuery.

JSON requests are made through a layer of indirection in the plugin so the results can be cached locally using Wordpress's built-in "transient" caching mechanism.

Upon enabling the plugin, the JSON formatted 'ALL' course list is automatically requested and cached. All other library location course lists as well as reserve items lists for each course are cached upon their initial request through the browser.


## Issues with Ares data

The data coming out of Ares is very messy and unpredictable. During the development of this code, the following issues were encountered:
- some courses are duplicated
- some courses don't have a name specified for them, just a course number
- some courses have the course number specified as the name also
- course id's can be assigned to multiple courses(!) (These are cross listed courses.)

The plugin tries to intelligently handle these cases, although new oddities are discovered all the time.


## Web Services Description

This plugin relies on Web Services maintained by John Fereira at Mann Library:
> http://coursereserves.library.cornell.edu/CourseReserves

For course reserves information, these Web Services communicate with the Ares system. The following are sample calls to those services.

To view a list of available library locations:
> http://coursereserves.library.cornell.edu/CourseReserves/showLocationInfo

The 'shortName' values from this output can then be used to request a list of courses associated with that library location:
> http://coursereserves.library.cornell.edu/CourseReserves/showCourseReserveList?library=Music

Note that in addition to the library locations returned by the showLocationInfo service, there is also an 'ALL' shortName that returns all courses for all library locations:
> http://coursereserves.library.cornell.edu/CourseReserves/showCourseReserveList?library=ALL

Finally, the 'courseNumber' value retrieved from the course list service can be used to request the reserve items for that course:
> http://coursereserves.library.cornell.edu/CourseReserves/showCourseReserveItemInfo?courseid=84


## Contributors

Adam Smith (ajs17@cornell.edu)

