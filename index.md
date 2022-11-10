---
layout: plain
title: Home

bookbeat: http://www.bookbeat.com/
stockholm: https://www.google.com/maps/place/Stockholm/@59.3258414,17.70188,10z/data=!3m1!4b1!4m5!3m4!1s0x465f763119640bcb:0xa80d27d3679d7766!8m2!3d59.3293235!4d18.0685808
---

<div class="home paper">
  <main class="page-content" aria-label="Content">
    <div class="wrapper">
      <section>
        <div>
          <img class="avatar" src="/assets/avatar.jpg" alt="Daniel Saidi" />
        </div>
        <div>
          <p>
            I'm a <a href="work">freelance</a> software engineer who specializes in mobile product development, with focus on Apple
            platforms like iOS, macOS, tvOS and watchOS and tech like Swift and SwiftUI.
          </p>
          <p>
            I love to make <a href="apps">apps</a>, <a href="open-source">open-source</a> and <a href="music">music</a>. I <a href="{{site.twitter_url}}">tweet</a> often, <a href="blog">blog</a> and give occasional <a href="talks">talks & workshops</a>.
          </p>
        </div>
      </section>
      <hr />
      <section class="links">
        {% include kankoda/grids/grid-title.html title="Links" %}
        {% include kankoda/grids/grid.html items=site.data.links type="icons" %}
      </section>
      <hr />
      <section class="work">
        {% assign work = site.data.work | slice: 0, 4 %}
        {% include kankoda/grids/grid-title.html title="Work" link="/work" %}
        {% include kankoda/grids/grid.html items=work link="work" type="icons" %}
      </section>
      <hr />
      <section class="open-source">
        {% assign open-source = site.data.open-source | slice: 0, 4 %}
        {% include kankoda/grids/grid-title.html title="Open Source" link="/open-source" %}
        {% include kankoda/grids/grid.html items=open-source link="open-source" type="icons" %}
      </section>
      <hr />
      <section class="apps">
        {% assign apps = site.data.apps | slice: 0, 4 %}
        {% include kankoda/grids/grid-title.html title="Apps" link="/apps" %}
        {% include kankoda/grids/grid.html items=apps link="apps" type="icons" %}
      </section>
    </div>
  </main>
</div>