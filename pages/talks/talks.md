---
layout: default
title: Talks
permalink: /talks/
---

<article class="card">
  {% include cards/header.html title="Talks" %}
  <p>Here is a list of all talks that I have done at meetups, company events etc. I haven't been recording them, but some were recorded by the event host. Just let me know if you'd like me to put together a talk for an event at your meetup, company etc.</p>
</article>

{%- assign talks = site.talks | sort: 'date' | where:'hidden',false | reverse -%}
{% for talk in talks %}
  {%- include talks/talk-list-item.html talk=talk -%}
{% endfor %}