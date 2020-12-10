---
layout: default
title: Talks - BookBeat
permalink: /talks/bookbeat
---

<article>
  <h1>Talks</h1>
  <p>Here is a list of all presentations that I've done at BookBeat since joining in September 2016.</p>
</article>

{%- assign talks = site.talks | where: 'location','BookBeat' | sort: 'date' | reverse -%}
{% for talk in talks %}
  {%- include talks/talk-list-item.html talk=talk -%}
{% endfor %}