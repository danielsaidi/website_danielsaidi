---
title: Running Facebook authentication on localhost
date:  2011-04-04 12:00:00 +0100
tags:  archive
---

I have started building an ASP.NET MVC3 site that uses the Facebook API to create and authorize users. It's easy to setup, but running it on localhost requires some configuration.

After creating a new Facebook application and pasting in the short code snippet that lets you log in with your Facebook account, I noticed I wasn't allowed to do so from localhost:

	API Error Code: 191
	API Error Description: The specified URL is not owned by the application
	Error Message: redirect_uri is not owned by the application.

I expected there to be a way to use the API locally while developing, and it turns out there it is. This is how you enable Facebook authentication locally:

- Create a web site, if you don't have one.
- Create an FB app that will be used by the site.
- Give the FB app a URL that you can manage, e.g. `http://myapp.com/`.
- If you upload some of the FB code snippets to this URL, they should work.

To enable FB features on localhost, you must create a web site with the same binding as the URL above. This is how you do it on IIS 7 / Windows 7:

- Setup a new web site in IIS, with the binding `http://myapp.com/`.
- Add 127.0.0.0 `http://myapp.com/` to your hosts configuration file.
- Now, navigate to the local web site by entering `http://myapp.com/`.
- If you add some of the FB code snippets to this local site, they should work.

Some FB features will however not work if the live site lacks certain pages. For instance, using a Like button to like a local page won't work if the URL doesn't exist on the live site.

