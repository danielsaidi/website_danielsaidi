---
title: Localization in Objective-C
date:  2012-08-20 22:17:00 +0100
tags:  objc localization
icon:  swift
---

This post will show you how to localize your iOS apps, so they can be translated
to several languages. I'll will describe how to translate plain text and how to
create localized versions of your storyboards.

Since storyboard localization comes with a few quirks, let's also discuss how to
automate synchronizing changes that are made in storyboards.


## Translating strings programmatically

Getting localization to work in iOS is pretty straightforward. You just have to
do the following:

* Add a new *Localizable.Strings* file to your project.

* In the file, add your key-value-based translations, like this (each line has 
to end with a semi-colon):

```objc
"key_1" = "Translation 1";
"another key" = "Another translation";
```

* To create a localized version of *Localizable.Strings*, select the file in the
Project Navigator and add localizations in the File Inspector to the right.

* When you have more than one version of this file, an arrow will appear next to
the file in the Project Navigator.

* Click the arrow to show all localized versions. Select any version in the list 
to modify that version.

Once you have your translations in place, use `NSLocalizedString(key, ...)` to
translate keys:

```objc
NSLocalizedString("key_1", nil)
```

This will return "Translation 1" if English is currently used. If you switch to
Swedish, the app would select the Swedish localization, if one exists. Any missing
keys will default to the translation in the base file.

That's really all there is to it. Before moving on, I just want to give you some
advice.

First, *do not use this approach to translate your storyboards*. This requires a
separate IBOutlet for each component that should be translated. If you just have
a few controls to translate, fine, but please read on to find out how to do this
in a better way.

Second, I want to advice against using translation key strings directly in code.
Doing so makes your app vulnerable, since a translation will fail if a string is
mistyped. These kinds of "bugs" are hard to find, since the Objective-C compiler
will not detect incorrect strings.

Instead, add a file that contains a definition for each key, as well as a 
translation macro with only one parameter (for convenience). In my latest app,
I call it *AppStrings.h*, and it looks something like this:

```objc
#define STR_CANCEL @"cancel"
#define STR_DELETE @"delete"
#define STR_OK @"ok"
...

#define Translate(key) \
[[NSBundle mainBundle] localizedStringForKey:(key) value:@"" table:nil]
```

As you see, this requires a little extra work, since you have to add a definition
for each translation key. However, it gives you a clean interface, where keys strings
are only used in this file. If a translation fails, this is the only place where it
can be misspelled, since all other classes will use these definitions.

You can then use the `Translate` macro (or the native NSLocalizedString, even if it
requires two parameters) together with any definition to translate your strings:

```objc
Translate(STR_CANCEL)
```


## Translating storyboards

Translating text programmatically was really easy. Now, let's see if translating
storyboards is as simple.

You basically have (at least) two options:

* Add an IBOutlet for each outlet that should be localizable, then translate it
programmatically using the approach above.

* Enable localization for your Storyboard and add a separate storyboard variant
for each language you have to support.

As I wrote above, don't use the first option if you have a lot of localized components 
in your storyboard. It will result in a lot of outlets. Instead, I suggest that you
enable localization by doing the following:

* Select your Storyboard in the Project Navigator

* To create a localized version of the file, select it in the Project Navigator
and add localizations in the File Inspector to the right.

* When you have more than one version of this file, an arrow will appear next to
the file in the Project Navigator.

* Click the arrow to show all localized versions of the storyboard.

You can now edit any version of the storyboard drectly. Translate texts, replace
icons etc. That's it - simple, eh? Well, not really. Once you have your versions
set up, you will bump into this:

* You have an original storyboard.

* You create a localized version of the storyboard.

* You make changes to the original storyboard, such as adding or removing something.

* BOOOOM! The changes will not be made to the localized version of the storyboard.

Since Xcode doesn't keep the storyboards in sync, localizing early will result
in tremendous amount of extra work. Each change has to be done in each localized
version...and that is just baaaaad.

Luckily, I found this great Python script that keeps your storyboards in sync:

[http://www.youtube.com/watch?v=cF1Rf02QvZQ](http://www.youtube.com/watch?v=cF1Rf02QvZQ)

Watch the video to find out how to make the script keep all storyboards in sync
each time the project is built. If you don't have five minutes to spare, you can
download the script [here](http://code.google.com/p/edim-mobile/source/browse/trunk/ios/IncrementalLocalization/localize.py) and add the following as a build step:

```python
python [path to the python script] --mainIdiom=[the main idiom] --mainStoryboard=[path to the main storyboard] [list of idioms to translate]
```


## Conclusion

To wrap up, localization is easy, but storyboards can mess up your workflow when
you change a lot in the storyboards.  I usually stick to localizing buttons, labels 
etc. with code instead.