---
title: Oredev 2012 - Day 2
date:  2012-11-12 21:30:00 +0100
tags:  conference
categories: conferences
---

My second day at Øredev 2012 was amazing! When it was over, I had to sit down and
take it all in. There were so many great talks, and I still had to skip many that
I wanted to see.


## Reginald Braithwaite – The Rebellion Imperative

After stating that wealth breeds ineffectiveness, that the powerful enforce stasis
and that we are not *in* the business, we *are* the business, Reginald quoted some
passages from "Marketing Warfare", which defines the four sustainable positions in
a market (there are other positions, but they are not sustainable):

* The leader
* The rival
* The innovator
* The 99%

These positions have different strategies:

* The leader **defends** - when Microsoft talks about their SharePoint Enterprise
Services Database Storage Platform, they are defending their position, simply by
saying "yeah, we've got that too...stay with us".
* The rival **attacks** - when Apple is considered to be the cool, Google makes a
point of Android being for the people who do not *have* to be cool.
* The innovator **disturbs** - by coming up with new ways, new models, new markets.
* The 99% **rebels** - and must watch out for the big trap of trying to play the
role of the innovator. Only one (the one who succeeds) can be the innovator. Many
can rebel.

Since the theme was rebellion, Reginald gave us some rebel advice. For instance,
rebels must not be emotionally attached to whatever they are doing, and be ready
to drop things that do not work. As an example he mentioned Audion, who abandoned
their mp3 software when iTunes was released.

I found this session to be really fun and thoughtful.


## Henrik Kniberg – Lean from the Trenches

Henrik's talk was a case study of introducing lean processes at the Swedish Police.
It was very interesting, but is kind of hard to summarize, so I'll keep it short.

Henrik's advice when you start out in a new project and don't know what to do, how
things work etc. is tp visualize what's going on. By asking people and putting it
all up on a board, you quickly get a clue of what's going on. Also, by discussing
the project, organization etc. with others in front of that board, they may learn
new things as well...or point out things that are just plain wrong.

Henrik and his team introduced incremental delivery. Optimizing for flow instead
of resource utilization (using a traffic jam analogy), features were only accepted
if they had customer value, if they were estimated (they only used small, medium
and large as estimation values) and if there were room for acceptance tests.

Henrik talked about so many things. It was all very interesting, but I think that
it's better that you watch the video, since I wouldn't do it justice by extracting
small bits from it. Instead, I'll wrap it up with Henrik's most critical factors
for project success: **co-location, incremental delivery and user involvement**.


## Johan Lindfors – Windows Phone Development Best Practices

I actually attended this session earlier this year, at DevSum, so I have already
written about. The reason I decided to watch it again, is that Johan surely will
have updated the presentation for Windows 8 and Windows Phone 8.

And he sure had. First, he talked about how strategies for estimating development
time, how to make your app stand out from iOS and Android apps (live tiles, pins
etc.) and how to handle beta testers. For instance, by measuring and awarding the
most active beta testers, you can get more out of them instead of ending up with
"testers" who just want your try latest app.

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

I am super-impressed that he actually did cover them all.

When managing **performance**, focus on the **perceived performance**. Even if a
task takes a long time, make it appear fast by doing stuff in background threads
and keeping your UI smooth. Use a splash screen when starting your app and make
sure it starts fast. If not, you can make the static splash shift to an animated
one that shows what is happening. Johan also implored keeping the fill rate (can
be done with a MemoryDiagnosticHelper addon) below 3, removing event subscribers
(otherwise, they will not be disposed) and force garbage collection, since it is
not forced by default.

For **communication**, make sure that your app use the static IsNetworkAvailable
property and adapt according to it. Johan talked about async patterns, timeouts
and retries and how to use the simulation dashboard (new) to simulate connection
speed, lock the screen etc.

For **data management**, Johan talked about settings, state and how to persist.
Like me, he almost never uses a database, but instead serialize objects and store
them in the isolated storage. I find this to be a very effective and nice way to
handle data on the phone.

For **design**, Johan demonstrated the grid (included by default in WP8) and how
to adjust your views so they align with the grid. He showed how to mock data in
design mode using XAML sample data, a fake data provider and by simply faking the
entire view model.

When it comes to **animations**, I find this to be a lot less fun in WP7 than it
is in iOS. However, I will once again checkout the resources Johan demonstrated,
namely Silverlight Toolkit for Windows Phone, WP7 Contrib (better animations than
in the former) and Metro in Motion.

Finally, to sum up the rest - Johan mentioned the same **MVVM** frameworks as he
did before and recommended us to use the SmartObservableCollection, since it can
disable events when large collections are updated. For **Localization**, he told
us to remember to localize the Marketplace material and showed us how to localize
an app without reloading it. For **testing**, he told us to use the Portable Class
Library. For **security**, he advised us to obfuscate our code before sending an
app to the Marketplace, since apps can be "unzipped".

Phew, that was a lot, and I still did not cover everything. It was a really great
session - a must-watch if you're into WP8 development!


## Steve Sanderson – Build Web Apps Faster

Steve Sanderson always entertain me extremely when he live coded. This time, the
focus was on how to build web applications faster.

Steve talked about how, though we have so many more great tools, technologies and
methods for developing systems today than we had a few years ago...well, it just
becomes too much. The last two systems I've built in .NET have been focusing more
on the system architecture and what's going on behind the scene, than to actually
create a working web app.

Maybe there is another way?

For many of my open source and web app presentation sites, I have begun throwing
the above away and just create simple one-pagers, sometimes just in HTML without
any backend whatsoever. However, going all that way has felt a bit...extreme, so
I was happy to watch Steve demonstrate something in-between.

Steve showed how we can create web applications using a **static site generator**
called **DocPad** (other alternatives are **Jekyll, Hyde and Punch**). This lets
you use site templates, partials etc. then compile it all into a static site that
just consists of html, js and css.

Steve used **Knockout** for model binding and **CoffeeScript** to inject things
into the template. It looked really slick.

Then, he blew me away as he demonstrated how to use the new Windows Azure Mobile
Services to avoid having a backend. While we almost always need a backend, do we
really need one on a server of our own? Windows Azure Mobile Services can handle
data management, authentication, push notifications, monitoring, scalability etc.
which most often take a *lot* of time for us to build.

Steve then went into the Azure Management Console and demonstrated how to create
a cloud-based database and how Azure can generate mobile application stubs for us.
For instance, iOS apps are already supported, and can be generated with a single
click. Android is on its way as well.

Steve then did some MacGyver magic to get the Azure MS API to work in JavaScript,
then built a cab booking system from scratch. It used Twitter authentication, a
cloud-based bookings database and simulated a cab route, so one could see a cab's
movement in real time, using Pusher for push notifications.

Before getting started with static sites, keep in mind that loading content with
JavaScript means that the site's crawlability is reduced. This means that static
sites are best suited for app-style sites. If you use a CMS, try tweaking it to
publish static pages to the static site of yours.

I my opinion, this talk was by far the most exciting and inspiring one at Øredev
2012!  Steve's was super slick and Azure Mobile Services seems awesome. This talk
is a must see!


## Oren Eini, Alistair Jones and Chris Harris – NoSQL FTW

In lack of other interesting sessions, I went to this talk and was (as expected)
gravely disappointed. It consisted of three lightning talks about RavenDB, neo4j
and CouchDB. I expected demonstrations. Instead, I got a sale pitch.

A bit of cred to Alistair Jones, however, for his cool neo4j demo that in detail
showed how to traverse a graph database.


## Steve Klabnik – Designing Hypermedia APIs

Starting his talk with general API discussions (**flexibility** vs. **stability**,
**decoupling**, **law on demeter** etc.), Steve moved on to a Hypermedia-specific
discussion.

According to Steve, the problem with many apis out there, is that they are based
on **out of band information**, which means information that is not included in
the information being exchanged between the server and the client. These kinds of
apis require good documentation outside of the api and that developers read up on
how to interact with them.

Hypermedia apis, however, provide (or at least should) its clients with all data
needed to interact with the api. For instance, the GitHub api returns pagination
links in the header. Besides defining media types, accepts etc. the header is a
great way to place this kind of additional information.

Steve also demonstrated how to build Hypermedia APIs, using media types etc. It
was a good session, but since I have seen most of it already, I should have gone
to another session. If you want to know more about Hypermedia apis, though, it's
a good watch.


## Shane Morris – Prototypes, prototypes, prototypes

Shane begun by asking us five reasons why we use prototypes...and we nailed them:

* **Validate** the concept in concrete terms
* **Try out ideas** with low risk
* **Identify issues** before it's too late
* **Sell the vision** to stakeholders and investors
* **Bring the team together** with a common thought

He had some fun architectural to prove his point, like how applications just like
buildings break at the joints. He discussed the pros and cons of the five points
above and talked a bit of different types of prototypes.

During development, we will begin to move from static prototypes (e.g. wireframes)
to more dynamic ones (e.g. dummy web sites). Since high fidelity prototypes are
expensive, start off with low fidelity ones and make sure that the prototypes are
**changeable**, **accessible** and **can evolve** over time.

This talk was very good, but should be watched rather than read.


## Jesus Rodriguez – Rocking the Enterprise with the Kinect Experience

This was the kind of presentation I expected last year, when Tim Huckaby gave us
more anecdotes and brief demos, than code that demoed with Kinect's capabilities.
Jesus started right away, showing us how to hack the new Kinect for Windows.

He then talked about gamification and how Kinect for Windows is not primarily for
games, but rather for **natural user interfaces**, which is one of the important
pillars in the next generation of user interfaces.

You need to design nuis differently than when designing games. For instance, the
unexpected is exciting and fun in game context, while in a nui it is frustrating.

Microsoft created Kinect for Windows after some hackers managed hacking the XBOX
360 Kinect to use for development. Still, it is not just a simple port. It has a
new, improved sensor, which provides you with:

* Skeletal tracking
* Depth information
* RGB data
* Facial tracking
* Speech processing

Jesus talked about these various capabilities, demonstrating how write code that
communicates with them. When you do, just create an instance of the sensor, then
enable each capability. Seems pretty straightforward.

For skeletal tracking, Kinect will support 20 joints when standing up and 10 when
sitting down. It can track 6 persons at once, although only two are tracked with
joints. The other will only be represented with a position. Remember to transform
the tracked joints with `TransformSmoothParameters`, to get smooth transitions.

The depth analysis component is used heavily by the other depth-related sensors
(the infrared data stream, for instance). It measures the distance in mm from the
Kinect Player and can be set to work in a **default mode** or **near mode**. The
mode should depend on how the user is expected to interact with the ui.

Jesus also demonstrated the RGB data stream (it's not that exciting, it's "just"
a camera) and the speech and grammar recognition. The speech part was a killer!
Besides working very well with initial grammar, you can register your own grammar
as well. For instance, Jesus shows how to make the Kinect take a photo and upload
it to Facebook with a simple spoken command. I wonder how it works in Swedish :/
Jesus then wrapped up the talk by demoing facial tracking and gesture interaction.

Yet another killer talk. If you're into Kinect development, it is a must see.


## Alexander Bard – The Rebels Come Out Online

I was not expecting much from Alexander Bard's keynote. As a Swede, knowing Alex
from the music scene with many so-so bands in his portfolio, I have no experience
of him as a speaker.

I was happy to be surprised :)

Alexander's talk was provocative, but highly clear-sighted and interesting. For
instance, he doesn't agree with the idea that our ideas form our technology. He
thinks it is the complete way around, that the technology we surround ourselves
with shapes our ideas. He sure backed that up with some really great examples.

I will not spoil anymore of this great keynote, since you simply **must** watch
it (I told you that Thursday was amazing...almost all sessions were magnificent).