---
title: Running Facebook authentication on localhost
date:  2011-04-04 12:00:00 +0100
tags:  .net authentication
icon:  dotnet
redirect_from: 
  - /blog/dotnet/2011/04/04/running-facebook-authentication-on-localhost
---

I have started building an ASP.NET MVC3 site that will use the Facebook API
to create and authorize users. It's really easy to setup, although running it
on localhost requires some configuration.

After creating a new Facebook application and pasting in the short code snippet
that lets you log in with your Facebook account, I noticed that I wasn't allowed
to do so from localhost:

	API Error Code: 191
	API Error Description: The specified URL is not owned by the application
	Error Message: redirect_uri is not owned by the application.

I expected there to be a way to use the API locally while developing...and it
turns out there it is. This is how you enable Facebook authentication locally:

- Create a web site, if you do not have one already.
- Create the FB app that will be used by the web site.
- Give the FB app an URL that you can manage, e.g. http://myapp.mydomain.com/.
- Make sure that the URL exists – it can be an empty folder, but it must exist and be public.
- If you upload some of the FB code snippets to this URL, they should work.

To enable FB features on localhost, you must create a web site with the same
binding as the URL above. This is how you do it on IIS 7 / Windows 7:

- Setup a new web site in IIS, with the binding http://myapp.mydomain.com/.
- Add 127.0.0.0 http://myapp.mydomain.com/ to your hosts file.
- Now, navigate to the local web site by entering http://myapp.mydomain.com/.
- If you add some of the FB code snippets to this local site, they should work.

That’s it! Some FB features will however not work if the live site lacks certain
pages. For instance, using a Like button to like a local page won't work if the
URL doesn't exist on the live site.

