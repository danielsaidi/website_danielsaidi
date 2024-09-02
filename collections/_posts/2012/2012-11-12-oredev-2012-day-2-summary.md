---
title: Oredev 2012 - Day 2
date:  2012-11-12 21:30:00 +0100
categories: conferences 
tags:  conferences
icon:  avatar
---

My second day at Øredev 2012 was amazing! When it was over, I had to sit down and take it all in. There were so many great talks, and I still had to skip many that I wanted to see.


## Reginald Braithwaite – The Rebellion Imperative

After stating that wealth breeds ineffectiveness, that the powerful enforce stasis and that we are not *in* the business, we *are* the business, Reginald quoted some passages from "Marketing Warfare", which defines the four sustainable positions in a market:

* The leader
* The rival
* The innovator
* The 99%

These positions have different strategies:

* The leader **defends** - when Microsoft talks about their SharePoint Enterprise Blahblah, they defend their position by saying "yeah,  we've got that too...stay with us".
* The rival **attacks** - when Apple is considered to be the cool, Google makes a point of Android being for the people who don't *have* to be cool.
* The innovator **disturbs** - by coming up with new ways, new models, new markets.
* The 99% **rebels** - and must watch out for the big trap of trying to play the role of the innovator. Only one (the one who succeeds) can be the innovator. Many can rebel.

Since the theme was rebellion, Reginald gave us some rebel advice. For instance, rebels must not be emotionally attached to whatever they are doing, and be ready to drop things that don't work. As an example he mentioned Audion, who abandoned their mp3 software when iTunes was released.

I found this session to be really fun and thoughtful.


## Henrik Kniberg – Lean from the Trenches

Henrik's talk was a case study of introducing lean processes at the Swedish Police. It was an interesting session, but hard to summarize. I'll keep it short.

Henrik's advice when you start out in a new project and don't know what to do, how things work, etc. is to visualize what's going on. By asking people and putting it all up on a board, you quickly get a clue of what's going on. By discussing the project, organization, etc. with others in front of that board, they may learn new things, and find things that are just wrong.

Henrik & his team introduced incremental delivery. Optimizing for flow instead of resource utilization (using a traffic jam analogy), features were only accepted if they had customer value, were estimated (they only used small, medium and large as estimates) and if there were room for acceptance tests.

Henrik talked about so many things. It was all very interesting, but I think that it's better that you watch the video, since I wouldn't do it justice by extracting small bits from it.


## Johan Lindfors – Windows Phone Development Best Practices

I attended this talk earlier this year, at DevSum, but decided to watch it again, since Johan may have updated the presentation for Windows 8 and Windows Phone 8.

And he sure had. He first talked about strategies for estimating development time, how to make your app stand out from iOS & Android apps and how to handle beta testers.

Just like last time, Johan had divided the talk into the following sections:

* Performance
* Communication
* Data management
* Design
* Animations
* MVVM
* Localization
* Testability
* Security
* Exceptions
* The Marketplace

I'm SUPER impressed that he actually managed to cover them all.

When managing **performance**, focus on the **perceived performance**. Even if a task takes a long time, make it appear fast by doing stuff on background threads and keeping your UI smooth. Use a splash screen to make the app launch seem faster, and make static splash screens shift to animated ones if you need more time.

Johan stressed keeping the fill rate below 3, removing event subscribers
(otherwise, they will not be disposed) and force garbage collection, since it is not forced by default.

For **communication**, make sure that your app use the static `IsNetworkAvailable` property and adapt according to it. Johan talked about async patterns, timeouts and retries and how to use the new simulation dashboard to simulate connection speed.

For **data management**, Johan talked about settings, state and how to persist. He almost never uses a database, but instead serializes objects and store them in isolated storage. I find this to be a very effective and nice way to handle data on the phone.

For **design**, Johan demonstrated the grid (included by default in WP8) and how to adjust your views to align with the grid. He showed how to mock data in design mode using XAML sample data, a fake data provider and by simply faking the entire view model.

For **animations**, I find this to be a lot less fun in WP7 than it is in iOS. However, I will once again checkout the resources Johan demonstrated, namely Silverlight Toolkit for Windows Phone, WP7 Contrib (better animations than in the former) and Metro in Motion.

For **Localization**, he told us to remember to localize the Marketplace material and showed us how to localize an  app without reloading it. For **testing**, he recommended the Portable Class Library  and for **security**, he advised us to obfuscate our code before sending an app to  the Marketplace, since apps can be "unzipped".

The rest of the talk, Johan mentioned some **MVVM** frameworks and recommended using the `SmartObservableCollection`, since it can disable events for large collections. 

Phew, that was a lot, and I still didn't cover everything. It was a great session, and a must-watch if you're into Windows Phone development!


## Steve Sanderson – Build Web Apps Faster

Steve Sanderson always entertain me when he live codes. This time, he focused on how to build web applications faster.

Steve talked about how, even though we have so many more great tools, technologies  and methods for developing systems, things can just become too much. Is there another way?

I've been considering this too, and for many of my open source and app presentation sites, I have begun throwing the above away and just create simple one-pagers, sometimes just in HTML without any backend whatsoever. However, this has felt a bit...extreme, so I was happy to watch Steve demonstrate something in-between.

Steve showed how we can create web apps using a **static site generator** called **DocPad** (other alternatives are **Jekyll, Hyde and Punch**). This lets us use site templates, partials, etc. then compile it all into a static site that just consists of html, js and css. 

Steve used **Knockout** for model binding and  **CoffeeScript** to inject things into templates. It looked really slick. He then blew me away by showing the new **Windows Azure Mobile Services**, which can be used as a backend, to handle data management, authentication, push notifications, monitoring, scalability, etc. All pre-built and ready to use.

Steve went into the Azure Management Console and showed how to create a cloud-based database and how Azure can generate mobile application stubs. iOS is already supported, and can be generated with a single click. Android is on its way as well.

Steve then did some MacGyver magic to get the Azure MS API to work in JavaScript, then built a cab-booking app from scratch, that used Twitter authentication and a cloud-based bookings database and simulated a cab route, so one could see a cab's movement in real time, using `Pusher` for notifications.

Before going all in on static sites, keep in mind that loading content with JavaScript means that the site's crawlability is reduced, which means that static sites are best suited for app-style sites. If you use a CMS, tweake it to publish static pages to the static site of yours.

This talk was by far the most exciting and inspiring one at Øredev 2012!  Steve's was super slick and Azure Mobile Services seems awesome. A must see!


## Oren Eini, Alistair Jones and Chris Harris – NoSQL FTW

In lack of other interesting sessions, I went to this talk and was (as expected) disappointed. It consisted of three lightning talks about RavenDB, neo4j & CouchDB. I expected demos, but got sales pitches.

Some cred to Alistair Jones, though, for his cool neo4j demo that in detail showed how to traverse a graph database.


## Steve Klabnik – Designing Hypermedia APIs

Starting his talk with API discussions (flexibility vs. stability, decoupling, Law of Demeter, etc.), Steve moved on to a Hypermedia-specific discussion.

To Steve, the problem with many APIs, is that they are based on **out  of band information**, which means information that isn't exchanged between the server and the client. These APIs require good documentation and that developers know how to interact with them.

Hypermedia APIs, however, provide (or at least should) clients with all the information that is needed to interact with them. For instance, the GitHub API returns pagination links in the header. Besides media types, accepts, etc. the header is a great place for such additional information. Steve also showed how to build Hypermedia APIs using media types. 


## Shane Morris – Prototypes, prototypes, prototypes

Shane begun by asking us five reasons why we use prototypes...and we nailed them:

* **Validate** the concept in concrete terms
* **Try out ideas** with low risk
* **Identify issues** before it's too late
* **Sell the vision** to stakeholders and investors
* **Bring the team together** with a common thought

Shane had some fun architectural analogies to prove his point, like how applications just like buildings break at the joints. He discussed pros & cons of the points above and talked a bit of different types of prototypes.

During the development phase, we start moving from static prototypes like wireframes, to more dynamic ones like interactive, fake web sites. Since high fidelity prototypes are very expensive, start with low fidelity ones and make sure that they are changeable, accessible and can evolve over time.


## Jesus Rodriguez – Rocking the Enterprise with the Kinect Experience

This was the session I expected last year, when Tim Huckaby focused more on anecdotes and brief demos, than showing Kinect's capabilities. Jesus started right away, showing us how to hack the new Kinect for Windows.

Jesus talked about gamification and how Kinect for Windows is not primarily for games, but rather for natural user interfaces, which is an important pillar in the next generation of user interfaces. You need to design NUIs differently than games. For instance, the unexpected is exciting and fun in a game context, while in a NUI it's frustrating.

Microsoft created Kinect for Windows after some hackers managed hacking the XBOX 360 Kinect for development. It's however not just a simple port, but has a new, improved sensor that provides you with amazing features, like:

* Skeletal tracking
* Depth information
* RGB data
* Facial tracking
* Speech processing

Jesus talked about these capabilities, and showed how write code that communicates with them. Just create an instance of the sensor, then enable each capability. Seems very easy.

For skeletal tracking, Kinect supports 20 joints when standing and 10 when
sitting. It can track 6 persons at once, where two are tracked with joints and the rest are represented with a position. Remember to transform the tracked joints with `TransformSmoothParameters`, to get smooth transitions.

The depth analysis component is used heavily by other depth-related sensors
(the infrared data stream, for instance). It measures distances in millimeters from the Kinect Player and can be set to work in a **default mode** or **near mode**. The mode should depend on how the user is expected to interact with the ui.

Jesus also demonstrated the RGB data stream and the speech and grammar recognition. The speech part was a killer! Besides working well with initial grammar, you can register your own grammar as well. For instance, Jesus shows how to make the  Kinect take a photo and upload it to Facebook with a simple spoken command.

Jesus wrapped up this amazing talk by demoing facial tracking and gesture interaction. If you're into Kinect development, it's a must see.


## Alexander Bard – The Rebels Come Out Online

I was not expecting much from this keynote. As a Swede, knowing Alex from the music scene with many so-so bands in his portfolio, I have no experience of him as a speaker.

I was happy to be surprised :)

Alexander's talk was provocative, but clear-sighted and interesting. For instance, he does not agree with the idea that our ideas form our technology. He thinks it's  the complete way around, that the technology we surround ourselves with shapes our ideas. He backed it up with some great examples, all very fun to watch.