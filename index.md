---
id: home
title: Home
layout: default

image: /assets/headers/website.jpg
image-show: 0

bookbeat: http://www.bookbeat.com/
stockholm: https://www.google.com/maps/place/Stockholm/@59.3258414,17.70188,10z/data=!3m1!4b1!4m5!3m4!1s0x465f763119640bcb:0xa80d27d3679d7766!8m2!3d59.3293235!4d18.0685808
---

<div class="home-content paper">
  <main class="page-content" aria-label="Content">
    <div class="wrapper">
      <section>
        <img class="avatar" src="/assets/avatar.jpg" alt="Daniel Saidi" />
        <div class="social-buttons">
          <p>
            {% include kankoda/buttons/social.html name="twitter" href=site.urls.twitter %}
            {% include kankoda/buttons/social.html name="mastodon" href=site.urls.mastodon %}
            {% include kankoda/buttons/social.html name="github" href=site.urls.github %}
            {% include kankoda/buttons/social.html name="linkedin" href=site.urls.linkedin %}
          </p>
        </div>
        <div>
          <p>
            I'm a <a href="work">freelance</a> engineer who specializes in app & product development, with focus on Apple platforms like iOS, macOS, tvOS & watchOS and tech like Swift & SwiftUI.
          </p>
          <p>
            I love building <a href="apps">apps</a> & <a href="opensource">open-source</a> tools. I <a href="blog">blog</a>, <a href="{{site.urls.twitter}}">tweet</a>, <a href="{{site.urls.mastodon}}">toot</a> and give occasional <a href="talks">talks</a>.
          </p>
        </div>
      </section>
      
      <hr />
      
      <section>
        {% include kankoda/titles/section.html title="Latest Posts" link="/blog" %}
        <div class="grid home centered">
          {% for post in site.posts limit:2 %}
          <div>
            {% include kankoda/blog/list-item.html post=posts.first %}
          </div>
          {% endfor %}
        </div>
      </section>
      
      <hr />
      
      <section class="opensource">
        {% assign projects = site.data.open-source | slice: 0, 4 %}
        {% include kankoda/titles/section.html title="Open Source" link="/opensource" %}
        {% include kankoda/grid/grid.html items=projects type="icons" %}
      </section>
      
      <section class="apps">
        {% assign apps = site.data.apps | slice: 0, 4 %}
        {% include kankoda/titles/section.html title="Apps" link="/apps" %}
        {% include kankoda/grid/grid.html items=apps type="icons" %}
      </section>
      
      <section class="work">
        {% assign work = site.data.work | slice: 0, 4 %}
        {% include kankoda/titles/section.html title="Work" link="/work" %}
        {% include kankoda/grid/grid.html items=work type="icons" %}
      </section>
    </div>
  </main>
</div>