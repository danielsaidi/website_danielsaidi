---
title: Grabbing the latest version number from a GitHub repository
date:  2011-10-05 12:00:00 +0100
tags:  git
icon:  github
---

I have several GitHub repositories, where some have a `gh-pages` branch with a public web site. On these web sites, I want to show and link to the latest version.

When I present the repository, I also want to make it easy to download the latest release. I therefore have a download button in which I want to show the latest version number.

To do this, I just use the GitHub API. Let’s say that we create a 2.1 version of a library. We would then create and and push a tag to GitHub:

```
git tag 2.1
git push origin 2.1
```

Once the tag is pushed, we can use the GitHub API to show & download the latest version.


## Use the GitHub API to grab all tags

The GitHub API let’s you do a lot. You can find all the information you need [here](http://develop.github.com/p/repo.html). With this, I decided to try fitzgen’s JavaScript `github-api` library to integrate with the API.

For instance, you just have write the following to grab all tags for a certain repository:

```
var repo = new gh.repo("danielsaidi", "Facadebook");
repo.tags(function(result){ alert(JSON.stringify(result)); });
```

Since I will use this approach for all GitHub repositories, I decided to package my custom script according to the rest of the JavaScript library.

I thus created another async method for the `gh.repo` prototype, like this:

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

Each site has a span element with the id `version`, which I can then modify like this:

```
$(document).ready(function() {
    var repo = new gh.repo("danielsaidi", "Facadebook");
    var tag = repo.getLatestRelease(function(result){ $("#version").html(result); });
    // Apply the tag to the #version span
});
```

When the page loads, the script loads all available repository tags, iterates over them and grab the latest tag. The result is rather nice:

![Cloney screenshot](/assets/blog/11/1005-1.png "A version number is now displayed within the download button")

This means that all repository web pages can feature a nice download button that shows the latest version number, just like other libraries like jQuery does.