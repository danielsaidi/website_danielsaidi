---
layout: default
title: Talks
permalink: /talks/
---

<article class="card">
  {% include cards/header.html title="Talks" %}
  <p>Here is a list of some talks that I have done at meetups, companies, events etc. Just let me know if you'd like me to talk at your event.</p>
</article>

{%- assign talks = site.talks | sort: 'date' | where:'hidden',false | reverse -%}
{% for talk in talks %}
  {%- include talks/talk-list-item.html talk=talk -%}
{% endfor %}