---
title: Grabbing the latest version number from a GitHub repository
date:  2011-10-05 12:00:00 +0100
tags:  github web open-source
icon:  github
---

I currently have several GitHub repositories, where some also have a `gh-pages`
branch with a public web site for each project. On these pages, I want to show
and link to the latest version.

On these pages, I present and drescribe the repository. I also want to make it
easy to download the latest release. I therefore have a big download button in
which I want to show the latest version number.

The answer is really simple. Use the GitHub API! For the example in this post
to work, each new version must be pushed as a tag to GitHub.

Let’s say that we want to create a 2.1.0 version of a library. We would then 
(after all other version operations) create and and push a tag to GitHub:

```
git tag 2.1.0
git push origin 2.1.0
```

We can now use the GitHub api to fetch and present the latest version.


## Use the GitHub API to grab all tags

The GitHub API let’s you do almost anything. You can find all the information
you need [here](http://develop.github.com/p/repo.html). However, instead of using 
jQuery to call the API, I decided to try fitzgen’s JavaScript `github-api` library.

To grab all tags for a certain repository, you just have write the following:

```
var repo = new gh.repo("danielsaidi", "Facadebook");
repo.tags(function(result){ alert(JSON.stringify(result)); });
```

Since I will use this for all GitHub repositories, I decided to package my custom
script according to the rest of the JavaScript library. I thus created another
async method for the `gh.repo` prototype, like this:

```
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
```

On each site, I have a span element with the id `version` and add the code
snippet below to the end of `github.js`:

```
$(document).ready(function() {
    var repo = new gh.repo("danielsaidi", "Facadebook");
    var tags = repo.getLatestRelease(function(result){ $("#version").html(result); });
});
```

That's is! When the page loads, th script loads all available repository tags,
iterates over them and grab the highest tag name. The result is rather nice:

![Cloney screenshot](/assets/blog/2011/2011-10-05.png "A version number is now displayed within the download button")

Hope this helps!