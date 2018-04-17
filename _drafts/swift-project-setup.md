These are the things I setup in new projects



* SwiftLint

A really important thing, bcs: ... Actually, a more
learning experience is to write your entire app, then
smack on SwiftLint at the very end...and see how your
illusions of yourself being a flawless developer shatters.

These are the rules I sidable, and why I disable them: ...

Add the run script step after target dependencies, so
you don't have to wait for the app to compile before lint.



* Localizations

Sure, your app may be unilingual today, but there will
come a day when you may want to localize, and to then
have language "keys" shattered all over your app is
NOT a nice experience. Localize from the get-go.

I use to:
- Create a Localizable.strings file
- Select the file and tap "Localize" in the right panel
- This moves the file
- Create a group without a folder called "Localization" and drag the file there.



* SwiftGen

SwiftGen is great bcs: ... I normally use it for localized
strings and image assets. 

You can install it with CocoaPods, which is nice, since it
forces everyone to use the same version. However, I use to
run with the latest in my own projects. You can install it
with homebrew.

After L10n is in place, I add the following run script phases
after SwiftLint:

Strings: [Place script here]
Assets: [Place script here]

Remember to adjust any paths that aren't in your project.

After you build your app for the first time, you have to add
the generated files to the project (I drag them to the same
folders as the rest). After that, you're done.



* App Icon

I use Sketch to creat emy app icons, and have setup an app
folder strurcture and Sketch template that I reuse. You can
download them here.

- App icon
- AppStore template
- Sticker Pack template