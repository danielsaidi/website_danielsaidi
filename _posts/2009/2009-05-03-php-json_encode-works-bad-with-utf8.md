---
title:  "PHP json_encode works bad with UTF8"
date:   2009-05-03 08:00:00 +0100
tags: 	php web
---


I have had some serious problems with UTF8 and PHP’s built-in JSON functionality.
After solving it, I realized that it was not even an UTF-8 issue, but a JSON one.

I faced the problems while working on Wigbi’s AJAX pipeline, that lets you execute
PHP functions with JavaScript, with a seamless and automatic 1:1 mapping. Almost
all functions – both static and instance ones – can be executed with a single line
of JavaScript, which makes it easy to develop powerful AJAX behavior with almost
no code at all.

When executing operations through the pipeline, I previously did the following:

* Build a JSON object with class name, possible object (for non-static functions), function name and parameters
* Send the JSON object together with some additional Wigbi data
* Decode the JSON object
* JSON-encode the parameters (returns an array string) and remove the start [ and end ]
* Build a string with the parameter string as function parameters and run eval on it
* However, the last encoding step turned out to “destroy” certain special characters

These special characters being destroyed, causes the dynamic function to use invalid
characters, thus failing to perform.

My new approach is to first collapse the parameter array to a string, instead of
encoding it. This works, but some adjustments are needed to support arrays and
objects. I will upload the collapse-version today and work on a more sophisticated
version tomorrow.

Many thanks to Mattias Sundberg for great and invaluable input!