---
title:  Schedule GitHub Pages rebuild with GitHub Actions
date:   2022-05-11 12:00:00 +0000
tags:   git github

assets: /assets/blog/2022/2022-05-11/
image:  /assets/blog/2022/2022-05-11/image.jpg

github: https://github.com
github-actions: https://github.com/features/actions
github-pages: https://pages.github.com
github-secret: https://docs.github.com/en/actions/security-guides/encrypted-secrets
jekyll: https://jekyllrb.com
front-matter: https://jekyllrb.com/docs/front-matter/
---

In this post, let's take a look at how to schedule a website with GitHub Actions, so that we can create future content that is automatically published at the intended publish date.

![GitHub Actions logo]({{page.image}})


## Background

As I've started writing on this blog more consistently, I aimed to publish new content at a certain time of the day. The best time to publish is still beyond me, but I hope to learn more over time.

I host this website on [GitHub]({{page.github}}) and use [GitHub Pages]({{page.github-pages}}) to build my content into a static website every time I push new content to it. It omis future content, which means that I can work on future content without it showing up on the site.

I use [Jekyll]({{page.jekyll}}), which is a static site generator that lets me define content as plain data and Markdown. This has boosted my productivity since rarely have to work with HTML, JavaScript and CSS.

Jekyll lets you define metadata like title, date, tags etc. in a page's [Front Matter]({{page.front-matter}}), which is a data section that you specify topmost in each page's Markdown file. For this page, the front matter looks like this:

```
---
title:  Schedule your website with GitHub Actions
date:   2022-05-11 12:00:00 +0000
tags:   git github

assets: /assets/blog/2022/2022-05-11/
image:  /assets/blog/2022/2022-05-11/image.jpg

github: https://github.com
github-pages: https://pages.github.com
jekyll: https://jekyllrb.com
front-matter: https://jekyllrb.com/docs/front-matter/
---
```

The `date` field format is UTC. I use it to specify the page's intended publish date. Since Jekyll ignores future content, future pages will not show up until this date has passed.


## The problem

The scheduled blogging works great, but one problem is that GitHub only rebuilds my website when I push. This means that I have to do a new `git push` for a future post to be published.

So even though Jekyll lets me create a bunch of future posts in advance, I still had to perform a manual push for any new content to show up on the website.

Obviously, this wouldn't work in the long run. I'd rather schedule my website to rebuild every now ant then, and started looking for a way to trigger a GitHub Pages rebuild. 

Turns out that [GitHub Actions]({{page.github-actions}}) is the perfect tool for the job.


## Setting up GitHub Actions

By setting up a scheduled GitHub Actions workflow with Curl and a cron job, you can trigger GitHub Pages to rebuild any time you like.

To make this work, first create a `.github` folder in the root, then add a `workflows` folder to that folder and a `.yml` file that you name after your workflow.

In my case, I added a `republish.yml` file like this:

![Finder screenshot]({{page.assets}}finder.jpg){:width="600px"}

The file contains all the information GitHub Actions need to perform the job:

```yml
name: Republish

on:
  schedule:
    - cron: '1 18 * * *'  # Run at 6:01 pm every day.
jobs:
  refresh:
    runs-on: ubuntu-latest
    steps:
      - name: Trigger GitHub pages rebuild
        run: |
          curl --fail --request POST \
            --url  https://api.github.com/repos/danielsaidi/website_danielsaidi/pages/builds \
            --header "Authorization: Bearer $USER_TOKEN"
        env:
          # You must create a personal token with repo access as GitHub does
          # not yet support server-to-server page builds.
          USER_TOKEN: ${{ secrets.USER_TOKEN }}
```

This file first defines the name of the workflow, which will show up in the GitHub Actions portal. It then tells the workflow to trigger every day at `6:01 pm`. 

> Note that the cron expression must be wrapped in **single quotations**!

The file then defines the jobs to run, which in this case only performs a `refresh` job with a single step that defines a name, a `run` command as well as an `env` field that specifies environment variables.

The `run` command triggers a `curl` command that performs a `POST` request to the `pages/builds` endpoint. For authentication, it provides an GitHub personal access token via a `USER_TOKEN` variable.

The final piece of the puzzle is to create a private access token (PAT), then create a GitHub repository secret to provide the token to the script.

To create a PAT, first go to your personal GitHub settings and locate "Developer settings" in the menu:

![GitHub Settings menu screenshot]({{page.assets}}github-settings-2.jpg){:width="400px"}

Create a new token and make sure to copy it! Once you close the creation form, you can't get it back.

The final step is to define the `USER_TOKEN` variable that we refer to in the workflow we created earlier. You define the environment variable as a [GitHub Secret]({{page.github-secret}}) under your repository settings:

![GitHub Secrets menu screenshot]({{page.assets}}github-secrets.jpg){:width="400px"}

Expand this section, pick `Actions` and create a new secret. In this case, name it `USER_TOKEN` and paste in the access token you just created.

That's it! You can now push the `republish.yml` file to have it trigger a republish at the times you have specified in the file. Once it triggers, it will show up in your repository's GitHub Actions portal:

![GitHub Actions Portal]({{page.assets}}github-actions.jpg)

The cron expression is very flexible, so you can setup your website to publish more or less exactly when you want it to. No more sitting around, waiting to publish, while others are having a fun Friday evening.


## Conclusion

I haven't used GitHub Actions before, and much of the information I found on making this specific task work was in many cases wrong or not adjusted for the specific quirks of GitHub Actions. 

If you too have struggled with this, I hope that this information helps you get started with GitHub Actions. It's a great tool, once you get the hang of it.