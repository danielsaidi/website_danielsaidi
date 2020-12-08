---
title: Running Facebook authentication on localhost
date:  2011-04-04 12:00:00 +0100
tags:  .net authentication
redirect_from: 
  - /blog/dotnet/2011/04/04/running-facebook-authentication-on-localhost
---

It has taken some time, but I have finally started building an ASP.NET MVC3 site
that will use the Facebook API to create and authorize users. It's a really easy
thing to accomplish, and I curse myself for not having a look at this earlier.

However, after creating a new Facebook application and pasting in the short code
snippet that lets you log in with your Facebook account, I noticed that I wasn't
allowed to do so from my localhost:

	API Error Code: 191
	API Error Description: The specified URL is not owned by the application
	Error Message: redirect_uri is not owned by the application.

Sure, you must enter an app URL when you register your app, but I expected it to
be possible to use the API while developing...and it turned out that it is. This
is how you do it:

- Create a web site, if you do not have one already
- Create the FB app that will be used by the web site
- Give the FB app an URL that you can manage, e.g. http://myapp.mydomain.com/
- Make sure that the URL exists – it can be an empty folder, but it must exist and be public
- If you upload some of the FB code snippets to this URL, they should work.

To enable the FB features on localhost, you must create a web site with the same
binding as the URL above. I only tried this with IIS 7 / Windows 7, so do let me
know if you make it work on Apache, in OS X etc.

- Setup a new web site in IIS, with the binding http://myapp.mydomain.com/
- Add 127.0.0.0 http://myapp.mydomain.com/ to your hosts file
- Now, navigate to the local web site by entering http://myapp.mydomain.com/
- If you add some of the FB code snippets to this local site, they should work.

That’s it. Some FB features will not work if the live site misses certain pages. 
or instance, if you use a Like button to like a local page, it will not work if
the URL does not exist at the live site.

