---
title:  Making Xcode and SPM trust a private SSH server
date:   2021-01-02 07:00:00 +0100
tags:   spm xcode git

assets: /assets/blog/21/0102/
---

In this post, let's take a look at `the server ssh fingerprint failed to verify` error, which may bite you if you add an SPM dependency from a private server, using SSH.


## The problem

I'm working on project that is hosted on a self-hosted GitLab server. As I did a first clone of the project from the Terminal, I got the following prompt:

```
RSA key fingerprint is xxx.
Are you sure you want to continue connecting (yes/no)? yes
```

If you approve this, the info is stored in `~.ssh/known-hosts`, after which your computer will trust the server and you will be able to clone, push, pull etc.

Cloning apps & packages from the server worked great from the Terminal, but when apps tried to pull in packages, Xcode failed with `the server ssh fingerprint failed to verify`:

![A screenshot of the error message]({{page.assets}}error.png){:width="500px"}

I verified that the private server was registered in `~.ssh/known-hosts`, that I still had SSH access to the server and all repos, logged in to the GitLab instance from Xcode, validated the SSH encryption (which differed between GitLab & GitHub), but nothing worked.


### The solution

After nearly giving up and resorting to another way of pulling in packages, I tried one last thing, which was to create a new Swift package and add one of the server packages as a package dependency. 

My hope was that it would show me a different error, since app projects and packages are handled differently...but I actually got something better:

![A screenshot of a trust dialog]({{page.assets}}trust.png){:width="250px"}

When I saved the package manifest file in Xcode, which makes Xcode resolve the external dependencies, I got the alert above, where I could choose to trust the server. After I clicked "Trust" in the alert, everything worked.


## Conclusion

This was a nasty problem with a strange workaround, that will hopefully be fixed in future versions of Xcode. However, it will hopefully help you if you run into the same problem.

If you know of another way to trigger the prompt or trigger the trust action with a terminal script, please share any knowledge you may have in the comment section below.