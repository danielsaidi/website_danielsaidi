---
title:  Using AI and Cursor to localize Xcode String Catalogs
date:   2025-06-08 07:00:00 +0000
tags:   xcode localization

assets: /assets/blog/25/0608/
image:  /assets/blog/25/0608/image.jpg

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lr4ml4oeqk2d
toot: https://mastodon.social/@danielsaidi/114649443676606699
---

Xcode String Catalogs are great for localization, but make it harder to add new languages compared to the old `.strings` files. Let's see how to export & import String catalog languages, and how to use Cursor to add support for new languages with AI.


## Creating a String Catalog

You can localize apps, SDKs, or any kind of target in Xcode, by adding a `String Catalog` to the target:

![A string Catalog Modal]({{page.assets}}string-catalog-modal.png){:class="plain"}

String Catalogs are amazing, and are very easy to localize. You can even vary translations based on devices, for instance to say "click" instead of "tap" on macOS.

You can easily add more languages to your String Catalog by tapping the bottom plus button and selecting a new language from the extensive list of supported languages.

![A string Catalog Add Button]({{page.assets}}string-catalog-add-button.png){:class="plain"}

This is where I faced some problems with how to best handle new languages. In the image below, I have just added Norwegian Bokmål to an SDK, with all Norwegian keys marked as `NEW`: 

![A string Catalog With Empty Norwegian]({{page.assets}}norwegian-empty.png)

If I had used the old `.strings` approach, I would have localized the separate Norwegian strings file by sending it to a translator or passing it to an AI-based translator.

This is not as easy with String Catalogs, where languages are added to a single XML file. Uploading the entire file to an AI service doesn't scale, and degrades over time, the more languages we add.

We can handle separate languages easier by exporting languages from the String Catalog, translate the separate files, then import the translated files back into the String Catalog.

Let's take a look at how to do this, and how we can use Cursor to translate new, untranslated files.


## Exporting Languages from a String Catalog

To export a language from a String Catalog, select the app or package from the sidebar, then select `Product > Export Localizations...` from the main menu.

![An export Menu]({{page.assets}}export-menu.png)

You will get a dialog, where you can select which languages to export and where to export them:

![An export Dialog]({{page.assets}}export-dialog.png){:class="plain"}

Each language will be exported to a separate `.xcloc` folder, which contains a few folders and files, including an `.xliff` file with the language-specific localizations.


## Using Cursor to translate XLIFF files

We can now open the export folder with Cursor and select the `.xliff` file we want to translate. In our case, the Norwegian Bokmål (nb) file is `nb.xcloc/Localized Contents/nb.xliff`.

![A Cursor window with the xliff file selected]({{page.assets}}cursor-untranslated.png){:class="plain"}

In the image above, you can see that the Norwegian Bokmål file only contains original English texts.

I first tried to ask Cursor to translate the entire `.xcloc` structure, but this was too complicated for it. I therefore selected the XLIFF file and asked Cursor to translate the `body` tag, and output the result:

> Translate the selected .xliff file's body tag to the target-language. For each text, keep the source tag and add a target tag with a `state="translated"` attribute and the translation as body. Output the result as plain text.

This will make Cursor output the result as plain text, instead of asking what to do with the result. I found this good in my case, since Cursor wasn't able to apply the result directly to the `.xliff` file:

![A Cursor window with the result ouput]({{page.assets}}cursor-result.png)

Once the operation has completed, just copy the output and replace the body tag in the original file:

![A Cursor window with the xliff file selected]({{page.assets}}cursor-translated.png){:class="plain"}

Finally save your changes. We are now ready to import our Norwegian translation back into Xcode.


## Importing Languages into a String Catalog

We can use Xcode to import the Norwegian translations back into our String Catalog. Select the app or package from the sidebar, then pick `Product > Import Localizations...` from the main menu.

![Xcode import menu]({{page.assets}}import-menu.png)

You will be prompted about which file to import. Select the `nb.xcloc` you previously exported. If it's correctly formatted, our Norwegian translations will be imported:

![A string Catalog With Norwegian Translations]({{page.assets}}norwegian-translated.png)

That's it! We have used Xcodes export & import together with Cursor to translate an entire app to a new language. We can probably automate it further. Please comment if you have any ideas on this.


## Conclusion

Xcode String Catalogs are amazing tools for working with localized content, but they're a little tricky compared to the old `.strings` files when it comes to adding support for new languages.

I hope that this post has clarified how to export & import languages with Xcode and given you some ideas on how to quickly translate an entire app to a new language. 

While this post uses Cursor to localize, a skilled human will of course give you a much better result.