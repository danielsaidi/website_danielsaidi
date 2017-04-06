---
title:  "Grabbing the latest version number from a GitHub repository"
date:    2011-10-05 12:00:00 +0100
categories: general
tags: 	github
---


I currently have several repositories at GitHub. For some of these repositories,
I have also created a `gh-pages` branch with a public web site for each project.

The public web site should present and rescribe the repository, and make it easy
to download the latest release. I therefore often have big download buttons that
say "Download My Library". However, I have yet not displayed a version number in
these buttons, which I would really like to do.

I thus set out to solve “how do I grab the latest version number from the GitHub
repository”. The answer is really simple. Use the GitHub API! For the example in
this post to work, each new version must be pushed as a tag to GitHub.

Let’s say that you have a new version (let’s say..hmmmm...2.1.0) of your project.
Now, create a tag for this version, using these two lines of git:

	git tag 2.1.0
	git push origin 2.1.0

This will create a new tag with the version number and push it to GitHub. Before
moving on, I want to emphasize that tags should use a name convention that makes
each new tag get a string value greater than one preceding it. If you name a tag
“release 0.1.0.0” and another “2.1.5.0”, the first will always be returned since
it will end up last in the list. No good.


## Use the GitHub API to grab all tags

The GitHub API is really slick, and let’s you do most anything possible. You can
find all the information you need [here](http://develop.github.com/p/repo.html).
However, instead of using jQuerying to call the API, I decided to try fitzgen’s
JavaScript `github-api` library.

To grab all tags for a certain repository, you just have write the following:

	var repo = new gh.repo("danielsaidi", "Facadebook");
	repo.tags(function(result){ alert(JSON.stringify(result)); });

Wow, that was easy! Now, let's grab the latest version number from the response.

Since I will use this approach for all GitHub repository web sites, I decided to
package my custom script according to the rest of the JavaScript library. I thus
created another async method for the gh.repo prototype, like this:

	gh.repo.prototype.getLatestRelease = function(callback) {
		this.tags(function(result) {
			var latest = "";
			for (var prop in result.tags) {
				if (prop > latest) {
					latest = prop;
				}
			}
			callback(latest);
		});
	}

On each site, I have a span element with the id “version”. I then added the code
snippet below to the end of github.js:

	$(document).ready(function() {
		var repo = new gh.repo("danielsaidi", "Facadebook");
		var tags = repo.getLatestRelease(function(result){ $("#version").html(result); });
	});

That is is! When the page loads, this script loads all available repository tags,
iterate over them and grab the “highest” tag name (version number).

The result is rather nice:

![Cloney screenshot](/assets/img/blog/2011-10-05.png "A version number is now displayed within the download button")

Hope this helps!