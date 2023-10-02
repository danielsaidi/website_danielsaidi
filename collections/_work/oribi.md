---
name: Oribi

assets: /assets/work/oribi-
image:  /assets/headers/oribi.jpg

okeyboard: https://oribi.se/program/okeyboard/
oribiwriter: https://oribi.se/program/oribi-writer/
---

Oribi develops powerful spelling aids and language support for the digital and physical world, including word prediction, spell checking, voice synthesizing and more.

{% include kankoda/data/open-source.html name="KeyboardKit" %}

![Oribi logo]({{page.image}})

Oribi reached out to me regarding my [KeyboardKit]({{project.url}}) project, and asked me to develop a custom keyboard app that makes use of their amazing technology.

The first product I developed for Oribi was [oKeyboard]({{page.okeyboard}}), which is a custom keyboard for iOS and iPadOS that applies Oribi's autocomplete, spellchecking and text-to-speech on top of the KeyboardKit engine.

![oKeyboard]({{page.assets}}okeyboard.png){:class="plain" width="550"}

As part of the oKeyboard project, I also structured Oribi's tools into separate libraries, which makes it easy to combine and use them in any app that use Oribi technologies.

After oKeyboard, I was trusted to upgrade their text editor for iPad - Oribi Writer - to a document-based app that is written in Swift and SwiftUI.

![Oribi Writer]({{page.assets}}oribiwriter.png){:class="plain"}

The new Oribi Writer builds on the powerful capabilities of document-based apps, and supports both iOS, iPadOS and macOS.

Thanks to Oribi, I was able to let KeyboardKit evolve as part of making oKeyboard great. Big thanks to Oribi for sponsporing KeyboardKit!