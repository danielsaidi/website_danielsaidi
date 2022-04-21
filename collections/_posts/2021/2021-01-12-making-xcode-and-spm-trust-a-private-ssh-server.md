---
title:  Making Xcode and SPM trust a private SSH server
date:   2021-01-02 07:00:00 +0100
tags:   article xcode spm
assets: /assets/blog/2021/2021-01-12/
---

In this post, let's look at a solution to the `the server ssh fingerprint failed to verify` error that may bite you when you add an SPM dependency to an app from a private server over SSH.


## The problem

I'm currently working on project that is hosted on a self-hosted GitLab server. As I did a first clone of the project from the Terminal, I got the following prompt:

```
RSA key fingerprint is xxx.
Are you sure you want to continue connecting (yes/no)? yes
```

When you approve this, the information is stored in `~.ssh/known-hosts`, after which your computer trusts the server and you will be able to clone, push, pull etc.

The private server is used for app projects as well as SPM packages that are used by the apps. Cloning the app and package repositories from the Terminal now worked great, but as the apps tried to pull in packages from the server, Xcode failed with `the server ssh fingerprint failed to verify`:

![A screenshot of the error message]({{page.assets}}error.png){:width="500px"}

I double-checked that the server was correctly registered in `~.ssh/known-hosts` and that I still had network and SSH access to the server and all repos. Everything was correctly registered, and worked from the Terminal, but not in Xcode.

I investivated many things, including logging in to the self-hosted GitLab instance from Xcode, checking the SSH encryption (which differed between GitLab and GitHub) etc, but nothing worked.


### The solution

After nearly giving up and resorting to another way of pulling in the library, I tried one last thing. 

I created a new Swift package and added one of the server packages as a package dependency. My hope was that it would show me a different error, since projects and packages are handled differently.

I got better:

![A screenshot of a trust dialog]({{page.assets}}trust.png){:width="250px"}

When I saved the package file in Xcode, which makes Xcode resolve external dependencies, I got an actual prompt, where I could choose to trust the server. After clicking "Trust", everything worked.


## Conclusion

This was a pretty nasty and unexpected workaround. However, it will probably and hopefully help you if you run into the same problem.

Why this prompt doesn't appear from the SPM dependency window is beyond me. It would have saved me many hours and a lot of hassle. 

If you know of another way to trigger the prompt or trigger the trust action with a terminal script, please share any knowledge you may have in the comment section below.