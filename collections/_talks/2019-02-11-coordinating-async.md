---
title: Coordinating Async Operations
date: 2019-02-11

location: CocoaHeads Stockholm
location_url: https://www.meetup.com/CocoaHeads-Stockholm/

post: http://danielsaidi.com/blog/2019/01/26/async-operations

tags: cocoaheads
tags-extra: swift
---

I gave this talk at [{{page.location}}]({{page.location_url}}) in 2019, on how to use protocols to coordinate concurrent and serial operations. We created a fully tested solution that is easy to use and understand.

<!--
<section data-markdown class="title-page">
  ## Coordinating Async Operations
  Daniel Saidi · [@danielsaidi]({{site.urls.twitter}})
</section>

<section data-markdown>
  # In this talk
  * Problem Definition
  * 3rd Party Libraries
  * Case Study
  * Live Coding
  * Discussions
</section>

<section data-markdown>
  # Why this talk?
  * Coordinating async operations is tricky
  * Serial vs. concurrent operations etc.
  * Limited native support
  * Completion blocks aren't enough
  * We need more sophisticated ways
</section>

<section>
  <section data-markdown>
      # 3rd party libraries
  </section>

  <section data-markdown>
    <script type="text/template">
      # PromiseKit

      Handle async operations with promises

<pre class="hljs" style="border-radius: 10px; display: block; overflow-x: auto; padding: 0.5em; background-color: rgb(40, 42, 54); color: rgb(248, 248, 242);">firstly {
    asyncRequest1()          <span class="hljs-comment" style="color: rgb(98, 114, 164);">// Returns a promise</span>
}.then { result1 <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">in</span>
    asyncRequest2(result1)   <span class="hljs-comment" style="color: rgb(98, 114, 164);">// Returns a promise</span>
}.then { result2 <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">in</span>
    doSomethingElse()       <span class="hljs-comment" style="color: rgb(98, 114, 164);">// Returns a promise</span>
}.<span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">catch</span> { error <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">in</span>
    <span class="hljs-comment" style="color: rgb(98, 114, 164);">// Handle any error</span>
}</pre>

      Easy to chain multiple operations
      
      Heavily block-based, pretty chatty
    </script>
  </section>

  <section data-markdown>
      <script type="text/template">
          # AwaitKit

          Handle async operations with async/await

<pre class="hljs" style="border-radius: 10px; display: block; overflow-x: auto; padding: 0.5em; background-color: rgb(40, 42, 54); color: rgb(248, 248, 242);"><span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">let</span> result1 = <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">try</span>! await(asyncRequest1())
<span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">let</span> result2 = <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">try</span>! await(asyncRequest2(result1))
<span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">try</span>! doSomethingElse()</pre>
          
          Looks like synchronous code (avoid try!)

          Based on PromiseKit, so you get 2 dependencies
      </script>
  </section>

  <section data-markdown>
      <script type="text/template">
      # RxSwift

      Handle async operations with observables

<pre class="hljs" style="border-radius: 10px; display: block; overflow-x: auto; padding: 0.5em; background-color: rgb(40, 42, 54); color: rgb(248, 248, 242);"><span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">let</span> searchResults = searchBar.rx.text.orEmpty
    .throttle(<span class="hljs-number">0.3</span>, scheduler: <span class="hljs-type" style="color: rgb(241, 250, 140); font-weight: 700;">MainScheduler</span>.instance)
    .distinctUntilChanged()
    .flatMapLatest { query -&gt; <span class="hljs-type" style="color: rgb(241, 250, 140); font-weight: 700;">Observable</span>&lt;[<span class="hljs-type" style="color: rgb(241, 250, 140); font-weight: 700;">Repository</span>]&gt; <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">in</span>
        <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">if</span> query.isEmpty {
            <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">return</span> .just([])
        }
        <span class="hljs-keyword" style="color: rgb(139, 233, 253); font-weight: 700;">return</span> searchGitHub(query)
            .catchErrorJustReturn([])
    }
    .observeOn(<span class="hljs-type" style="color: rgb(241, 250, 140); font-weight: 700;">MainScheduler</span>.instance)</pre>
      
      Has many other tools, e.g. binding
      
      A major commitment, will affect your architecture
      
    </script>
  </section>

  <section data-markdown>
    # Conclusion
    * There are many 3rd party libraries for this
    * Many are very powerful
    * Many will affect your architecture
    * Sometimes 3rd party libraries are not an option
  </section>
</section>


<section>
  <section data-markdown>
    # Case study
  </section>

  <section data-markdown>
    # BookBeat
    * Extensive offline support, for instance:
    * Book actions (add/remove, listen/read, etc.)
    * Bookmarks
    * Analytics
    * Sync/update when the app comes online
  </section>

  <section data-markdown>
    # Sync/update
    * Sync book actions (one by one, serial)
    * Sync bookmarks (batches, concurrent)
    * Sync analytics (batches, concurrent)
    * Fetch latest data (many sources, concurrent)
  </section>

  <section data-markdown>
    # Problem
    * Completely different needs for each operation
    * Some operate on items, others on batches
    * Some are serial, others concurrent
    * Most must be coordinated, others can run in bg
    * We don't want to use 3rd party dependencies
  </section>

  <section data-markdown>
    # Approach
    * Describe the operations as protocols
    * Find a good way to compose protocols
    * Provide as much functionality as possible
    * While still allowing customizations
    * Composition over inheritance
  </section>
</section>

<section data-markdown>
  # Live Coding
</section>

<section data-markdown>
  # Conclusions
</section>

<section data-markdown>
  # Thank you!
  ## Questions?
  Daniel Saidi · [@danielsaidi]({{site.urls.twitter}})
</section>
-->