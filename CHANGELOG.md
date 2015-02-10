# Chastity Changelog

### 0.1.3 (2015-02-10)

* **[FIXED]** The Redis lock driver now reloads LUA scripts if they are not already present on the server

### 0.1.2 (2015-01-31)

* **[IMPROVED]** Reduced log noise by removing "lock request" log message
* **[IMPROVED]** Reduced log noise by suppressing failure message for lock polls (ie, when timeout is zero)

### 0.1.1 (2014-12-15)

* **[FIXED]** Removed unnecessary SET operation from redis-extend.lua

### 0.1.0 (2014-11-17)

* Initial release
