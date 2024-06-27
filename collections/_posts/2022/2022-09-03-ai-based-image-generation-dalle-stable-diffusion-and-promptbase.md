---
title:  AI-based image generation with DALL·E, Stable Diffusion and Promptbase
date:   2022-09-03 00:00:00 +0000
tags:   open-ai ai ml

icon:   blog
image:  https://i.imgur.com/06D3jgk.png
image-show: 0

assets: /assets/blog/22/0903/

jbagley: https://twitter.com/JBagley
jbagley-tweet: https://twitter.com/JBagley/status/1565764754618159106?s=20&t=0Jsa9dz8EVNZZq3HYicFmg
levelsio: https://twitter.com/levelsio
levelsio-tweet: https://twitter.com/levelsio/status/1565747606143336450?s=20&t=0Jsa9dz8EVNZZq3HYicFmg
chasm: https://en.wikipedia.org/wiki/Crossing_the_Chasm
dall-e: https://openai.com/dall-e-2/
stable-diffusion-tweet: https://twitter.com/levelsio/status/1565731907664478209?s=20&t=UlBJZG9I0vhTkvsPNn_Bow
promptbase: https://promptbase.com
---

I'm a bit late to the AI party, but just wanted to write down some thoughts about [DALL·E 2]({{page.dall-e}}), [Stable Diffusion]({{page.stable-diffusion-tweet}}) and the ecosystems of services that follow, like [PromptBase]({{page.promptbase}}).

As many of you, I was excited to see tweets about DALL·E starting to pop up, showing how AI-based image generation has reached a new level of quality.

Although generative AI has been around for a while, being able to go to a website, type a sentence into a text field and generate an AI-based image in seconds is something new, and makes this amazing functionality available to the masses.

As people started getting access to DALL·E (and raging over the steep pricing model), the Internet overflowed with creations. To me, this level of commonfolk engagement in an AI-based service is unprecedented. Dare I say that DALL·E made AI finally [cross the chasm]({{page.chasm}})?

However, as I started playing around with it, it soon became clear that it's not as simple as typing a general sentence and pressing a button. Going from typing "a unicorn riding on a corn cob in a corn field" into DALL·E and getting this:

![a unicorn riding on a corn cob in a corn field]({{page.assets}}unicorn.jpg)

to creating a professional, hi-quality image obviously requires additional instructions and solid prompting skills, to be able todescribe exactly what you want.

Another generative AI tool that have popped up, is Stable Diffusion. [This tweet]({{page.stable-diffusion-tweet}}) makes a amazing job of explaining how to use it. 

However, as I read through the thread and its comments, [levelsio]({{page.levelsio}}) had [this great comment]({{page.levelsio-tweet}}):

![Tweet saying "Biggest trick I learnt is, you can't just write a blonde guy"]({{page.assets}}levelsio-tweet.jpg){:width="450px"}

That's a very valid point. When you think of it, telling DALL·E to generate "a unicorn riding on a corn cob in a corn field" and becoming disappointed with the output is pretty unfair. 

The generative AI actually solves the task flawlessly. The problem is that the sentence only defines the object and setting, and conveys nothing about the desired style, lightning, etc. It gives the AI very little to work with. Shit in, shit out.

Mastering the tool is still as important as ever, and is where my AI-based image generation journey (if you can call following a series of tweets a journey) took a new, unexpected turn, as I read this [this reply]({{page.jbagley-tweet}}) from [jbagley]({{page.jbagley}}):

![Tweet about PromptBase"]({{page.assets}}jbagley-tweet.jpg){:width="450px"}

Turns out that there is now a service called [Promptbase]({{page.promptbase}}) that lets you "find top prompts, produce better results, save on API costs, sell your own prompts".

In short, these services sell pre-defined prompts that you can use to make services like DALL·E generate more stable results, with a more cohesive style. For instance, you can buy a prompt that generates clay-styled emojis.

This made my day. To see humanity gather around a new piece of technology and come up with entirely new kinds of services is truly inspiring. Life finds a way.

I doubt I will use AI-based images in a real-life project any time soon, but the future sure looks promising. If you have any stories to tell about your own experiences with these groundbreaking technologies and services, I'd be very interested in hearing about them.