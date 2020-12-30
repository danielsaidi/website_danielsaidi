---
layout: plain
title: Home

bookbeat: http://www.bookbeat.com/
stockholm: https://www.google.com/maps/place/Stockholm/@59.3258414,17.70188,10z/data=!3m1!4b1!4m5!3m4!1s0x465f763119640bcb:0xa80d27d3679d7766!8m2!3d59.3293235!4d18.0685808
---

<div class="home">
  <main class="page-content" aria-label="Content">
    <div class="wrapper">
      <section>
        <div>
          <img class="avatar" src="/assets/avatar.jpg" alt="Daniel Saidi" />
        </div>
        <div>
          <p>
            I am a <a href="work">freelance software engineer</a> in <a href="{{page.stockholm}}">Stockholm, Sweden</a>. I specialize in mobile product development with focus on Apple technologies like iOS, tvOS, watchOS, Swift and SwiftUI. I have also worked with .NET, web and api development for 15+ years.
          </p>
          <p>
            I love coding and to build <a href="apps">apps</a> and <a href="open-source">open source projects</a>. I <a href="{{site.twitter_url}}">tweet</a> often, <a href="blog">blog</a> seldom and enjoy giving occasional <a href="talks">talks</a>. I also used to make a lot of <a href="music">music</a>.
          </p>
        </div>
      </section>
      <hr />
      <section class="links">
        <h2>Links</h2>
        {%- include grid.html items=site.data.links type="icons" -%}
      </section>
      <hr />
      <section class="work">
        <h2>Work</h2>
        {%- include grid.html items=site.data.work type="icons" -%}
      </section>
      <hr />
      <section class="open-source">
        <h2>Open Source</h2>
        {%- include grid.html items=site.data.open-source type="icons" -%}
      </section>
      <hr />
      <section class="apps">
        <h2>Apps</h2>
        {%- include grid.html items=site.data.apps type="icons" -%}
      </section>
    </div>
  </main>
</div>