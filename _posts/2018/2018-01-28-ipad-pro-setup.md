---
title:  "iPad Pro production setup"
date:   2018-12-28 10:00:00 +0100
tags:	git github workingcopy
---

In this post, I’ll write about my long process to find a setup that lets me be more productive and flexible in how I work with my various sites, projects, blog etc. I will start with describing how I moved away from my old legacy hosting and blog engine and end with how I finally found home in a setup, that lets me create content and push changes to GitHub on my iPad Pro.

## Moving away from legacy hosting/blogging
Not that long ago, my web site hosting and blog setup was a lot different than it is now. Back then, I used an old hosting provider for my various web sites and was forced to use FTP to “publish” any changes I made. Since I had already started using cloud-based services at home and at work a lot earlier, this setup was just a frustrating old habit I was stuck with.

Regarding my blog, I started blogging at Blogger loooong ago and was very happy to (also long ago) move over to Wordpress. However, as time passed and the blog started having more code in it, the Wordpress platform limitations started showing, with every post having some tiny variation in style, code blocks caused strange side-effects in the underlying markup etc. I needed something like LaTeX, but for the web.

Two years ago, I finally decided to break the wheel. Instead of staying with my hosting provider, I created static versions of my various sites and moved them to GitHub, using GitHub’s excellent hosting capabilities. Since GitHub has free domain configuration, I could use my existing domain names. The entire process was a very positive experience, that ended with me now being able to git push my changes, instead of dragging files in an FTP client like a cave man from the 90s.

The static web site setup was just a temporary step I had to go through to move my web sites before the old hosting had to be renewed for another year. Once they were in place, I could take the next step and rebuild them with Jekyll, which is a static site generator that works great with GitHub. It lets you create a bunch of pages, data files, templates, blog posts etc. and then “compile” them to a static site that consists of HTML, CSS and JS. It’s a very powerful setup, with the only drawback that you lack a proper backend, which makes it a non-option for many types of web sites. However, if you can use a static site for your web site, I really recommend that you give Jekyll a try. The speed of a static site is excellent and you can use various tricks and tweaks to make the setup very powerful. I do not miss having a backend for a second.

If you want to learn more about Jekyll, [visit their web site](https://jekyllrb.com).

With Jekyll in place, I could now perform the last step of this transition - moving my blog from Wordpress to my personal web site. Since Jekyll lets you create pages and blog posts with [Markdown](https://daringfireball.net/projects/markdown/) (hey, it’s like LaTeX for the web!), I could finally stop focusing on the look of my blog posts, and focus on its content instead (well, more on that later).

The end result is a really nice setup, that lets me focus on what I want to do when I want to do it. Being able to push changes with git even for blog posts and site changes is amazing, Jekyll is a great static site generator and Markdown is a wonderful format that removes the HTML complexity from the creative process. If you haven’t tried these technologies, I strongly advice you to check them out.

## iPad Pro - the missing piece
However nice the setup ended up to be, one thing that I was still missing was the possibility to work with my blog and various projects on my iPad Pro, which I bought as a lighter laptop version, to take with me on trips, conferences etc. The iPad Pro and its smart keyboard cover is soooo nice, but I just couldn’t find a productive and fun setup.

Another thing that made me hesitate was that all these things that you can do for free on a laptop cost money on an iPad, in the form of apps. That put me off initially, but I was (of course) always aware of the fact that I was an app developer who often complains about people’s inability to pay for apps...and still hesitated to spend a couple of $$$ on a few apps who together could help me boost my creativity. Wtf is wrong with me.

So, tonight, I decided to give it another go, with the initial goal to find a nice blog setup.

## Setting up my iPad Pro for blogging
So, as I took on the task to get this final piece in place, I set out to find a good git client and a text editor with MarkDown support. 

For git, I decided to give [WorkingCopy](https://workingcopyapp.com/) another try, after abandoning it a few months ago. It’s free to download, but requires a +$10 IAP to unlock push capabilities, but I decided to pay it and...it’s just a wonderful client.

For text editing, I read John Sundell’s tweet about using [iAWriter](https://ia.net/writer) and bought it as well...and it’s just a wonderful text editor. I am amazed with how intuitive they have made the user interface - everything just comes naturally.

Once you have cloned your repo in WorkingCopy, you can open any document with iAWriter. I am currently writing this blog post there right now, so if you can read this, it means that iAWriter could save the document back to WorkingCopy and that I then could push the new post to GitHub using WorkingCopy.

If so, I have finally found a nice setup, with private GitHub repos as the final piece that will let me write on various projects on my iPad and iAWriter, instead of using bloated document apps like Google Docs, Pages or Word.

All the best

Daniel Saidi