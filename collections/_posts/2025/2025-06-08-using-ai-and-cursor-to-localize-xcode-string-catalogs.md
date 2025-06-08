---
title:  Using AI and Cursor to localize Xcode String Catalogs
date:   2025-06-08 07:00:00 +0000
tags:   xcode localization

assets: /assets/blog/25/0608/
image:  /assets/blog/25/0608/image.jpg

redirect_from: /blog/2025/06/01/Introducing-FlipKit

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lqmiokcxdk2l
toot: https://mastodon.social/@danielsaidi/114613135306183949
---

Xcode String Catalogs are great for managing localizations, but a little harder to translate for new languages compared to the old `.strings` files. In this post we'll take a look at how to export and import languages from a String Catalog, and how to use Cursor to add support for new languages.


## Creating a String Catalog

You can localize apps, SDKs, or any kind of target in Xcode, by adding a `String Catalog` to the target:

![A string Catalog Modal]({{page.assets}}string-catalog-modal.png){:class="plain"}

String Catalogs are amazing, and are very easy to localize. You can even vary translations based on devices, for instance to say "click" instead of "tap" on macOS.

You can easily add more languages to your String Catalog by tapping the bottom plus button and selecting a new language from the extensive list of supported languages.

![A string Catalog Add Button]({{page.assets}}string-catalog-add-button.png){:class="plain"}

This is where I faced some problems with how to best localize new languages. In the image below, I have just added Norwegian Bokmål to an SDK, with all keys marked as `NEW`: 

![A string Catalog With Empty Norwegian]({{page.assets}}norwegian-empty.png)

If I had used the old `Localized.strings` approach, I would just have taken the separate Norwegian file and easily localized it with any AI service. 

This is however not as easy with a String Catalog, since it contains all locations in a single XML file. Uploading the entire catalog to an AI service doesn't scale, and becomes worse over time.

To make this easier, we will export a single language from the String Catalog, use AI and Cursor to translate it, then import it back into the package.


## Exporting Languages from a String Catalog

To export a language from a String Catalog, select the app or package from the sidebar, then select `Product > Export Localizations...` from the main menu.

![An export Menu]({{page.assets}}export-menu.png)

You will get a dialog in which you can select which languages to export and where to export them:

![An export Dialog]({{page.assets}}export-dialog.png){:class="plain"}

This will export an `.xcloc` folder for every locale, which contains a few folders and files, including an `.xliff` file with the language-specific localizations.


## Using Cursor to translate the xliff file

We can now open the exported folder with Cursor and navigate to the `.xliff` file that we want to translate. In our case, the Norwegian Bokmål (nb) file is `nb.xcloc/Localized Contents/nb.xliff`.

Here, you can see that the Norwegian Bokmål content only contains the original English texts.

![A Cursor window with the xliff file selected]({{page.assets}}cursor-untranslated.png){:class="plain"}

We can now ask Cursor to translate the content into Norwegian Bokmål. I first tried to ask Cursor to translate the entire `.xcloc` structure, but this was too complicated for it.

Instead, select the `nb.xliff` file and ask Cursor to only translate the `body` part of the XML. Don't ask it to replace the content of the file (it failed for me) but to give you the translated result as plain text.

I used the following prompt to make Cursor perform the task as expected:

Translate the selected .xliff file's body tag to the target-language. For each text, keep the source tag and add a target tag with a `state="translated"` attribute and the translation as body. Output the result as plain text.

This makes Cursor output the result as plain text, instead of asking you what to do with the result. This was good in my case, since Cursor wasn't able to apply the result directly to the `.xliff` file:

![A Cursor window with the result ouput]({{page.assets}}cursor-result.png)

Once the operation has completed, just copy the output and replace the body tag in the original file:

![A Cursor window with the xliff file selected]({{page.assets}}cursor-translated.png){:class="plain"}

Finally save your changes. We are now ready to import our Norwegian translation back into Xcode.


## Importing Languages into a String Catalog

We can now use Xcode to import the Norwegian translations back into our String Catalog. Select the app or package from the sidebar, then pick `Product > Import Localizations...` from the main menu.

![Xcode import menu]({{page.assets}}import-menu.png)

You will now be prompted about which file to import. Select the `nb.xcloc` folder from where you previously exported it. If everything is correct, our Norwegian translations will be imported:

![A string Catalog With Norwegian Translations]({{page.assets}}norwegian-translated.png)


## Conclusion

Xcode String Catalogs are amazing tools for working with localized content, but they're a bit more tricky to scale compared to the old `.strings` files.

I hope that this post has clarified how to export and import languages from a string catalog. While this post uses Cursor to localize, a skilled human will of course give you a much better result, that you can trust.