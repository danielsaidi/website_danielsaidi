---
name: Oribi

assets: /assets/work/oribi-
image:  /assets/headers/oribi.jpg

okeyboard: https://oribi.se/program/okeyboard/
oribiwriter: https://oribi.se/program/oribi-writer/
---

Oribi develops powerful spelling aids & language support for the digital and physical world, including word prediction, spell checking, voice synthesizing and more.

{% include kankoda/data/open-source.html name="KeyboardKit" %}

![Oribi logo]({{page.image}})

Oribi reached out to me regarding my [KeyboardKit]({{project.url}}) project, and asked me to develop a custom keyboard app that makes use of their amazing technology.

The first product I developed for Oribi was [oKeyboard]({{page.okeyboard}}), which is a custom keyboard for iOS & iPadOS that adds Oribi's autocomplete, spellchecking & text-to-speech to a keyboard.

![oKeyboard]({{page.assets}}okeyboard.png){:class="plain" width="550"}

As part of the oKeyboard project, I also structured Oribi's tools into separate libraries, which makes it easy to combine and use them in any app that use Oribi technologies.

After oKeyboard, I rebuilt Oribi Writer from scratch, to a document-based SwiftUI app, that runs on iOS, iPadOS and macOS:

![Oribi Writer]({{page.assets}}oribiwriter.png){:class="plain"}

Thanks to Oribi, I could focus on improving KeyboardKit as part of making oKeyboard.