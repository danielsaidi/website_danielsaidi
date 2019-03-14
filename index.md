---
layout: plain
title: Home

bookbeat: http://www.bookbeat.com/
twitter: http://www.twitter.com/danielsaidi
---

<div class="home">
  <main class="page-content" aria-label="Content">
    <div class="wrapper">
      <section class="me">
        <div>
          <img class="avatar" src="/assets/avatar.jpg" alt="Daniel Saidi" />
        </div>
        <div class="card">
          <p>
            I am a software engineer in Stockholm, Sweden, where I currently work as iOS Lead at <a href="{{page.bookbeat}}">BookBeat</a>. I have also worked with .NET, web and apis for 15+ years.
          </p>
          <p>
            I love coding and maintain some <a href="apps">apps</a> and <a href="open-source">open source projects</a>. I <a href="{{page.twitter}}">tweet</a> often, <a href="{{page.blog}}">blog</a> seldom and enjoy giving occasional <a href="talks">talks</a> at
            meetups and companies.
          </p>
        </div>
      </section>
    </div>
  </main>

  <a name="links"></a>
  {%- include home-grid.html title="Links" items=site.data.links -%}

  <a name="apps"></a>
  {%- include home-grid.html title="Apps" items=site.data.apps -%}

  <a name="open-source"></a>
  {%- include home-grid.html title="Open Source" items=site.data.open-source -%}
</div>