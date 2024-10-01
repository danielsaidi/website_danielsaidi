---
layout: default
title: Products
permalink: /products
---

<article>
  {% include kankoda/buttons/home.html %}

  <h1>Products</h1>

  <p>
    Here are some products that I have created for my company <a href="https://kankoda.com">Kankoda</a>. Kankoda creates commercial and closed-source SDKs, as well as apps.
  </p>

  {% include work-paragraph.html %}
</article>

{% include kankoda/grid/grid.html items=site.data.products type="icons" %}