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
        <section class="card">
          <p>
            I am a software engineer in Stockholm, Sweden, where I currently work as iOS Lead at <a href="{{page.bookbeat}}">BookBeat</a>. I have also worked with .NET, web and apis for 15+ years.
          </p>
          <p>
            I love coding and to build <a href="apps">apps</a> and <a href="open-source">open source projects</a>. I <a href="{{page.twitter}}">tweet</a> often, <a href="blog">blog</a> seldom and enjoy giving occasional <a href="talks">talks</a>. I also used to make a lot of <a href="music">music</a>.
          </p>
        </section>
      </section>
    </div>
  </main>

  <a name="links"></a>
  {%- include cards/grid-section.html title="Links" items=site.data.links -%}

  <a name="apps"></a>
  {%- include cards/grid-section.html title="Apps" items=site.data.apps -%}

  <a name="open-source"></a>
  {%- include cards/grid-section.html title="Open Source" items=site.data.open-source -%}
</div>