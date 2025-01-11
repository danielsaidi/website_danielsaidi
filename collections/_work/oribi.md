---
name: Oribi

assets: /assets/work/oribi-
image:  /assets/headers/oribi.jpg

okeyboard: https://oribi.se/program/okeyboard/
oribiwriter: https://oribi.se/program/oribi-writer/
---

Oribi develops powerful spelling aids & language support for the digital and physical world, including word prediction, spell checking, voice synthesizing and more.

{% include kankoda/data/open-source name="KeyboardKit" %}

![Oribi logo]({{page.image}})

Oribi reached out regarding [KeyboardKit]({{project.url}}), and asked me to create a custom keyboard app powered by their amazing language technology.

The first product I developed for Oribi was [oKeyboard]({{page.okeyboard}}), which is a custom keyboard for iOS & iPadOS that adds Oribi's autocomplete, spellchecking & text-to-speech:

![oKeyboard]({{page.assets}}okeyboard.png){:class="plain" width="550"}

As part of this project, I structured Oribi's tools into separate libraries, which makes it easy to combine and use them in any app that use Oribi technologies.

After oKeyboard, I rebuilt their Oribi Writer app from scratch, to a multi-platform, document-based app that runs on iOS, iPadOS and macOS:

![Oribi Writer]({{page.assets}}oribiwriter.png){:class="plain"}

Thanks to Oribi, I was able to improve KeyboardKit a lot, as part of making oKeyboard. It was a true pleasure to work with them for so many years.