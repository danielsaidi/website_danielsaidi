---
title: Validate Localized Resources
date:  2020-10-20 14:00:00 +0100
tags:  swift contiuous-integration
icon:  swift

fastlane: https://fastlane.tools 
swiftgen: https://github.com/SwiftGen/SwiftGen
lokalise: https://lokalise.com
---

In this post, we'll look at a way to validate app localizations and integrate it into [Fastlane]({{page.fastlane}}) and any CI processes you may have.


## Background

Localization is always a good idea, even when working alone on an app with a single language. Separating localized texts from your code makes the code easier to read and the texts easier to maintain. You app will also only be a translation away from supporting more languages.

When you work on bigger apps, chances are that they support multiple languages, each with it's own set of localized resources. In these cases, you probably use a cloud-based tool like [Lokalise]({{page.lokalise}}) to let a team of translators translate your app without access to the raw resources.

If you then add new language keys with temp texts until the translators have had a chance to translate them, you probably don't want the untranslated texts to accidentally make their way out into production, just because you forgot to download the latest translations.

To prevent this from happening, you should verify that your localized files don't contain empty strings, and make it a part of your release process.


## grep to the rescue

You can use `grep` to check for the occurrence of a string within a file. The following checks for empty keys within an English localization file in a certain folder:

```
grep "= \"\";" <PATH TO LOCALIZED FOLDER>/en.lproj/Localizable.strings
```

This will output all empty translations, if any, or exit if the file doesn't contain any empty keys. We can assign this to a build script variable, to control parts of our build process.


## Fastlane to the rescue

If you use Fastlane to automate various parts of your development process, you can add a new lane that helps you validate a localized file:

```
lane :l10n_validate do |options|
  locale = options[:locale]
  if locale == nil or locale.empty?
    UI.user_error!("Missing parameter 'locale'")
  end
  file = "<PATH TO LOCALIZED FOLDER>/" + locale + ".lproj/Localizable.strings"
  if !sh('cd .. && grep "= \"\";" ' + file + ' || true').empty?
    UI.user_error!(file + " has empty translations!")
  end
end
```

You can now call this lane from the Terminal:

```
fastlane l10n_validate locale:de
```

as well as from any other lanes. If the file contains any empty translations, Fastlane will fail with a red error text.

If your app supports multiple languages, you can add a lane that validates all localized files:

```
lane :l10n_validate_all do |options|
  l10n_validate(locale: "da")
  l10n_validate(locale: "de")
  l10n_validate(locale: "en")
  l10n_validate(locale: "fi")
  l10n_validate(locale: "pl")
  l10n_validate(locale: "sv")
end
```

You can now run `l10n_validate_all` from the Terminal or from any other lane to abort operations that require all translations.

This makes it possible for you to for instance abort a release distribution to App Store Connect.


## Temporary translations

You probably use temporary strings for your main development language, to avoid having empty strings in the app until translations are done. If so, I'd suggest that you establish a certain text pattern that you always use in your temporary strings, e.g. a adding a certain word like "Todo" or "Temp" that otherwise aren't used in real translations.

This lets you add a second `grep` to your validation, that detects any occurrences of that word:

```
lane :l10n_validate do |options|
  locale = options[:locale]
  if locale == nil or locale.empty?
    UI.user_error!("Missing parameter 'locale'")
  end
  file = "<PATH TO LOCALIZED FOLDER>/" + locale + ".lproj/Localizable.strings"
  if !sh('cd .. && grep "= \"\";" ' + file + ' || true').empty?
    UI.user_error!(file + " has empty translations!")
  end
  if !sh('cd .. && grep <TERM> ' + file + ' || true').empty?
    UI.user_error!(file + " has temp translations!")
  end
end
```

You can naturally take this even further to include other localized resources, for instance `Localized.stringsdict` files.


## Conclusion

Validating your app's localized resources is a good practice to protect your app from being released with temporary or missing translations. I hope that you find this approach easy and straightforward to implement.