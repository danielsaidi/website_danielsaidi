---
name: Oribi

assets: /assets/work/oribi-
image:  /assets/work/oribi-title.png

keyboardkit: https://getkeyboardkit.com
okeyboard: https://oribi.se/program/okeyboard/
oribiwriter: https://oribi.se/program/oribi-writer/
---

Oribi develops powerful spelling aids and other types of language support for the digital and physical world. They provide tools like word prediction, spell checking, voice synthesizing and more.

![Oribi logo]({{page.image}})

Oribi first hired me for keyboard-related work and are heavily sponsoring my [KeyboardKit]({{page.keyboardKit}}) open source project, which has been drastically improved during this project.


## oKeyboard

The first product I developed for Oribi was [oKeyboard]({{page.okeyboard}}), which is a custom keyboard for iOS and iPadOS. It builds on KeyboardKit and adds features like autocomplete, spellchecking, text-to-speech etc.

![oKeyboard]({{page.assets}}okeyboard.png){:class="plain"}

As part of oKeyboard, I implemented much of Oribi's various tools in standalone libraries, which makes it easy to compose functionality in apps that make use of their technologies.


## OribiWriter

After wrapping up oKeyboard and freelancing for Bambuser for 6 months, I rejoined Oribi to bump their text editor for iOS - OribiWriter - to a document-based app written in Swift and SwiftUI 3.

![Oribi Writer]({{page.assets}}oribiwriter.png){:class="plain"}

The new version shipped in early 2022 and leverages much of Oribi's features in a convenient text editor. At the time of writing, Oribi Writer is also being developed for macOS.


## Conclusion

Thanks to Oribi, [KeyboardKit]({{page.keyboardKit}}) has evolved a lot, with many new features being developed for both the core foundation and the SwiftUI parts of the library. Big thanks to Oribi for sponsporing [KeyboardKit]({{page.keyboardKit}})!