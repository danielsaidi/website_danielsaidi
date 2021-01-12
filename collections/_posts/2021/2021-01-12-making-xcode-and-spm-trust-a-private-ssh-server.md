---
title:  Making Xcode and SPM trust a private SSH server
date:   2021-01-02 07:00:00 +0100
tags:   swift xcode spm
assets: /assets/blog/2021/2021-01-12/
---

In this post, I'll provide a solution to the dreaded `the server ssh fingerprint failed to verify` error that may bite you when you add an SPM dependency to an app from a private server over SSH, using an IP address and port.


## The problem

I'm currently working on project that is hosted on a self-hosted GitLab server, as descibed above. As I did a first clone from the server, I got the following:

```
RSA key fingerprint is xxx.
Are you sure you want to continue connecting (yes/no)? yes
```

When you go through this, the information is eventually stored in `~.ssh/known-hosts`, after which your computer trusts the server and you will be able to clone, push, pull etc.

I did the above, then added a new SPM repo to the GitLab server. No problemo. However, as I then tried to add the library to one of the apps, Xcode failed with `the server ssh fingerprint failed to verify`, which looks like this:

![A screenshot of the error message]({{page.assets}}error.png)

I double-checked that the server was correctly registered in `~.ssh/known-hosts` and that I still had access to the server and the repo. Everything worked, except adding the SPM dependency. 

Xcode just refused.

I investivated many things, including logging in to the self-hosted GitLab instance from Xcode, checking the SSH encryption (which differed between GitLab and GitHub) etc, but nothing worked.


### The (a) solution

After nearly giving up and resorting to another way of pulling in the library, I tried one last thing. 

I created a new SPM library and added the first library as a package dependency. My idea was that this would show me a more detailed error log or message. 

I got better:

![A screenshot of a trust dialog]({{page.assets}}trust.png)

When I saved the package file, which makes Xcode resolve external dependencies, I got a prompt! I actually got a prompt, where I could choose to trust the server. 

I chose to trust the server. After this, everything worked.


## Conclusion

This was a pretty nasty and unexpected workaround. However, it will probably and hopefully help you if you run into the same problem.

Why this prompt doesn't appear from the SPM dependency window is beyond me. It would have saved me many hours and a lot of hassle. 

If you know of another way to trigger the prompt or trigger the trust action with a terminal script, please share any knowledge you may have in the comment section below.