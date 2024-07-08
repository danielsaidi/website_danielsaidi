---
title: Localization in Objective-C
date:  2012-08-20 22:17:00 +0100
tags:  archive localization
icon:  swift
---

This post shows how to localize your iOS apps in several locales. I'll will describe how to translate plain text and how to create localized versions of your storyboards.

Since storyboard localization comes with a few quirks, let's also discuss how to automate synchronizing changes that are made in storyboards.


## Translating strings programmatically

Setting up localization in iOS is pretty straightforward. You just have to do the following:

* Add a new *Localizable.Strings* file to your project.
* In the file, add key-value-based translations, like this one:

```objc
"key_1" = "Translation 1";
"another key" = "Another translation";
```

* To add more locales, select *Localizable.Strings* in the Project Navigator.
* You can now add new localizations in the File Inspector to the right.
* When the file supports multiple locales, an arrow will appear next to it.
* Click the arrow to show all localized versions.
* Select any version in the list to modify that version.

With your translations in place, use `NSLocalizedString(key, ...)` to translate keys:

```objc
NSLocalizedString("key_1", nil)
```

This returns "Translation 1" if English is used. If you switch to Swedish, the app will select the Swedish localization, if any. Any missing keys will default to the base file.

That's really all there is to it. Before moving on, I just want to give you some advice.

Don't use this approach for storyboards. It requires a separate IBOutlet for each localized component. If you have a few controls, fine, but if you have many, there is a better way.

Second, I want to advice against using translation key strings directly in code. Doing so makes your app vulnerable, since a translation will fail if a string is mistyped. These kinds of "bugs" are hard to find, since the Objective-C compiler will not detect incorrect strings.

Instead, add a file that contains a definition for each key, as well as a translation macro. In my latest app, I call it *AppStrings.h*, and it looks something like this:

```objc
#define STR_CANCEL @"cancel"
#define STR_DELETE @"delete"
#define STR_OK @"ok"
...

#define Translate(key) \
[[NSBundle mainBundle] localizedStringForKey:(key) value:@"" table:nil]
```

This requires a little extra work, since you have to add a definition for each translation key. However, it gives you a clean interface, where keys strings are only used in this file. 

You can then use the `Translate` macro (or the native NSLocalizedString, even if it requires two parameters) together with any definition to translate your strings:

```objc
Translate(STR_CANCEL)
```


## Translating storyboards

Translating text was pretty easy. Now, let's see if translating storyboards is as simple.

You basically have (at least) two options:

1. Add an IBOutlet for each component that should be localized, then translate it with the approach we just discussed above.
2. Enable localization for your Storyboard and add a separate storyboard variant for each language you have to support.

Don't use the first option if you have a lot of localized components, since it requires a lot of outlets. Instead, I suggest that you enable localization by doing the following:

* Select your Storyboard in the Project Navigator.
* Just as with `Localizable.strings`, add more locales in the File Inspector to the right.
* When the file supports multiple locales, an arrow will appear next to it.
* Click the arrow to show all localized versions.

You can now edit any version of the storyboard directly, e.g. translate texts, replace icons, etc. That simple, eh? Well, not really. Once you have multiple versions, you run into this:

* You have an original storyboard.
* You create a localized version of the storyboard.
* You make changes to the original storyboard, such as adding or removing something.
* The changes will not be reflected in the localized version of the storyboard.

Since Xcode doesn't keep the storyboards in sync, localizing early will result in tremendous amount of extra work. Each change has to be done in each localized version. Too bad.

Luckily, I found this great Python script that keeps your storyboards in sync:

[http://www.youtube.com/watch?v=cF1Rf02QvZQ](http://www.youtube.com/watch?v=cF1Rf02QvZQ)

The video shows how the script keeps all storyboards in sync each time the project is built. You can download it [here](http://code.google.com/p/edim-mobile/source/browse/trunk/ios/IncrementalLocalization/localize.py) and add the following as a build step:

```python
python [path to the python script] --mainIdiom=[the main idiom] --mainStoryboard=[path to the main storyboard] [list of idioms to translate]
```


## Conclusion

To wrap up, localization is easy, but storyboards can mess up your workflow if you change a lot in the storyboards.  I usually stick to localizing buttons, labels etc. with code instead.