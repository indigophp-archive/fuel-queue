Queue package
=============

Package to manage queues based on the well-known resque's php fork php-resque. Resque is a Redis-backed library for creating background job, placing them on multiple queues.

For development and fallback purposes, there is a 'direct' driver, which performs the jobs immediately instead of queueing them. Please keep in mind that it might slow down your application, as these jobs are run during the page load.