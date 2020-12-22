---
title: iPad Pro production setup
date:  2018-12-28 10:00:00 +0100
tags:  git github jekyll

jekyll: https://jekyllrb.com
markdown: https://daringfireball.net/projects/markdown
workingcopy: https://workingcopyapp.com/
iawriter: https://ia.net/writer
---

In this post, I’ll write about my process to find a setup that lets me be more productive and flexible in how I work with my various sites, projects, blog etc. I will descibe how I moved away from my old hosting provider and Wordpress and how I finally found a setup that lets me create content on my iPad Pro.


## Moving away from legacy hosting

Not that long ago, my web hosting setup was a lot different than it is now. Back then, I used an old hosting provider for my sites, that forced me to use FTP to publish any changes I made. Since I already used cloud-based services for other projects, this setup was just a frustrating old habit that I had to change.

Two years ago, I finally decided to do something about all this. Instead of staying with my old hosting provider for yet another year, I created static html versions of my sites and moved them to GitHub, using its excellent hosting capabilities. Since GitHub has amazing (and free) domain configuration, I could use my existing domain names. The entire process was a very positive experience, and I can now push my changes with git, instead of using an FTP client like a cave man.

Once these static sites were in place, I could take the next step and rebuild them with [Jekyll]({{page.jekyll}}), which is a static site generator that works great with GitHub. It lets you create pages, data files, templates, blog posts etc. then “compiles” them to a static site that only consists of HTML, CSS and JS. It’s a very powerful setup, with the only drawback that you lack a backend, which makes it a non-option for many web sites. However, if you *can* use a static site, I can recommend Jekyll. The speed of a static site is excellent and you can use various tricks and tweaks to make the setup very powerful. I do not miss having a backend.

The end result is a nice setup that lets me push changes with git instead of using an FTP client. If you haven't tried even GitHub hosting in combination with Jekyll,
I strongly advice you to give it a try.


## Moving away from Wordpress

I started blogging at Blogger many years ago and was very happy when I replaced it with Wordpress (that also many years ago). However, as time passed, the Wordpress limitations started showing, with every post having some tiny style and markup variation, code blocks caused strange side-effects in the underlying markup etc. I needed something like LaTeX, but for the web.

After moving my personal web site to GitHub, I decided to move my blog to this web site as well, making it a separate section of my web site. Since Jekyll supports [Markdown]({{page.markdown}}) (which is like LaTeX, but for the web), I could now finally start focusing on content and stop fiddling around with html markup.

The end result is a nice setup that lets me focus on what I want to do when I want to do it. Being able to push changes with git even for blog posts is amazing, and also gives me version control over my posts. Markdown is a wonderful format that removes the HTML complexity from the creative process. If you haven’t tried it out, I strongly advice you to do so.


## Blogging on my iPad Pro

With these changes in place, I still missed being able to create content with my iPad Pro, which I purchased as a lightweight laptop replacement. It is such an amazing device and its smart keyboard cover is so nice, but I previously haven't been able to setup a productive setup on it.

So, tonight I decided to give it a go, with the goal to find a great git client and a text editor that can interact with this client and edit files in my git repos. 

For git, I decided to try [WorkingCopy]({{page.workingcopy}}). It is free to download and try out, but you have to unlock push capabilities and pro features with an IAP. I purchased this IAP and found WorkingCopy to be a wonderful client. It is intuitive and makes great use of the iPad environment.

For text editing, I decided to purchase [iAWriter]({{page.iawriter}}) and...well, it's one of the best iOS apps I've ever tried. I am amazed with how intuitive it is. Writing is a pure joy, its Markdown support is amazing and I really could not ask for more.

The interactions between the two apps also proved to work exceptionally well. Once you have cloned a repo in WorkingCopy, you can open any file in iAWriter. I am writing this post like that, so if you can read this, it means that iAWriter and WorkingCopy could save and push it to GitHub...and that my search is finally over.