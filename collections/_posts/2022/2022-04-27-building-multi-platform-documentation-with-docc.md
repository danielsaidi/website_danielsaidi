---
title:  Building multi-platform documentation with DocC
date:   2022-04-27 07:00:00 +0100
tags:   article swift docc

image:  /assets/blog/2022/2022-04-27/image.jpg
assets: /assets/blog/2022/2022-04-27/

docc: https://developer.apple.com/documentation/docc
fastlane: https://fastlane.tools
swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

DocC is an amazing tool for writing and generating documentation for Swift-based projects. This post will discuss how to generate multi-platform documentation with DocC, using Terminal scripts and Fastlane.

![DocC icon]({{page.image}})

This post assumes that you are familiar with Swift packages and DocC. If not, you can have a look at [the DocC website]({{page.docc}}) for more information and [SwiftUIKit]({{page.swiftuikit}}) for an example package.


## The documentation catalog

You add DocC documentation to a Swift Package by adding a `Documentation Catalog` in Xcode:

![Xcode - add documentation catalog]({{page.assets}}xcode.jpg)

The Documentation Catalog should have the same name as your package and have a Markdown file with the same name in the root. For SwiftUIKit, it looks like this:

![SwiftUIKit documentation catalog]({{page.assets}}xcode-swiftuikit.jpg)

Whenever you generate documentation, DocC will use this Markdown file as start page, which can be used to link to the types in the library, additional articles and tutorials etc.


## Generate documentation from Xcode

In Xcode, you can build a `Documentation Archive` from your documentation catalog, with `Product > Build Documentation` or its keyboard shortcut.

If your package supports multiple platforms, just select a simulator for the platform you want to generate documentation for. The generated archive will then be specific to that particular platform.

While this is nice, you may also want to generate documentation as part of your build process. Let's look at a way to achieve this with some scripts and Fastlane.


## Generate documentation from the Terminal

If you just want to generate documentation for your package from the Terminal, the script is pretty basic:

```sh
xcodebuild docbuild \
    -scheme SwiftUIKit \
    -destination 'generic/platform=ios'
```

This will generate a documentation archive for iOS in Derived Data. There are a bunch of options, but this is the most basic way to do it. You can replace `ios` with `OS X`, `tvOS` and `watchOS` to generate archives for other platforms as well. 

Once you have an archive, you can generate a static website from it, that can be hosted on e.g. GitHub:

```sh
$(xcrun --find docc) process-archive \
    transform-for-static-hosting PATH_TO_ARCHIVE \
    --output-path Docs/web \
    --hosting-base-path SwiftUIKit
```

This will generate a static website in a `Docs/web` folder, which you can then add to your `gh-pages` branch and push to GitHub. Just make sure to setup GitHub Pages for your repository.

While these scripts are super simple, there is a pretty new DocC plugin that makes things even easier. Let's take a look at how it works.


## Generate documentation using the DocC plugin

The DocC plugin can be added to a Swift Package by adding this dependency to the package definition:

```swift
dependencies: [
    .package(url: "https://github.com/apple/swift-docc-plugin", from: "1.0.0"),
]
```

This lets you build documentation with `swift package` instead of `xcodebuild` and `xcrun --find docc`. For instance, you can generate a website without first generating a documentation archive:

```sh
swift package \
    --allow-writing-to-directory Docs \
    generate-documentation \
    --disable-indexing \
    --transform-for-static-hosting \
    --hosting-base-path SwiftUIKit \
    --output-path Docs/web
```

This will generate a static documentation website in `Docs/web`.

You can also start a local web server and preview the website with a single command:

```sh
swift package \
    --disable-sandbox \
    preview-documentation \
    --transform-for-static-hosting \
    --hosting-base-path SwiftUIKit \
    --output-path Docs/web
```

While this is great, I could however not find a way to specify platform. This means that the commands above only generate documentation for macOS. 

For SwiftUIKit, which supports iOS, macOS, tvOS and watchOS, I'd prefer the documentation to support all platforms, but it that's not possible, I at least want the online documentation to be iOS-specific.

I have looked everywhere for a way to provide platform, but haven't found a way to do so. Until I find a way, or the plugin adds this capability, I therefore had to find another way.

Since I have automated my work process with Fastlane, I therefore created a bunch of lanes that let me generate multi-platform documentation with a single command. Let's take a look at how this was done.


## Generate multi-platform documentation with scripts and Fastlane

If you're not familiar with [Fastlane]({{page.fastlane}}), it's basically a scripting tool that can be used to automate your development and release process. I use it for all my apps and libraries.

I now want to extend the Fastlane setup for my Swift packages with a bunch of lanes that let me generate DocC documentation archives and static web sites, using a single command if possible.

To avoid that the setup becomes too Fastlane-specific, I will use the `sh` function to call regular scripts that you could call from the Terminal as well, without involving Fastlane.

You will notice that the final setup contains more logic than just calling the scripts as above. We have to locate generated archives, clean up stuff etc. so our lanes will be a bit more complex.


### Step 1: Generate a platform-specific documentation archive

First, let's create a `docc_platform` lane that generates a documentation archive for a certain platform:

```sh
desc "Build documentation for a single platform"
lane :docc_platform do |values|
  sh('cd .. && mkdir -p Docs')
  docc_delete_derived_data
  sh('cd .. && xcodebuild docbuild \
    -scheme SwiftUIKit \
    -destination \'generic/platform=' + values[:destination] + '\'')
  sh('cd .. && \
    find ~/Library/Developer/Xcode/DerivedData \
    -name "SwiftUIKit.doccarchive" \
    -exec cp -R {} Docs \;')
  sh('cd .. && \
    mv Docs/SwiftUIKit.doccarchive Docs/SwiftUIKit_' + values[:name] + '.doccarchive')
end
```

As you can see in all lanes, the `sh` function actually executes in the `Fastlane` folder. This means that we have to add `cd .. &&` before all scripts to ensure that they are executed in the project root.

This script first creates a `Docs` folder, if none exists. It then calls a `docc_delete_derived_data` that looks like this:

```sh
desc "Delete documentation derived data (may be historic duplicates)"
lane :docc_delete_derived_data do
  sh('find ~/Library/Developer/Xcode/DerivedData \
    -name "SwiftUIKit.doccarchive" \
    -exec rm -Rf {} \; || true')
end
```

This function locates and deletes all `SwiftUIKit.doccarchive` in the global Derived Data folder. This is needed since there may be many and we must have exactly one for later steps. `|| true` is added to silence any errors that will otherwise cause Fastlane to abort.

The `docc_platform` lane then runs `xcodebuild docbuild` to generate a documentation archive for a platform that is specified with a `values[:destination]` parameter, which can be `ios`, `OS X` etc.

Once the archive is generated, the lane runs `find` to find the (now guaranteed only) documentation archive in Derived Data and moves it to the local `Docs` folder.

You can specify a custom derived data folder when generating the archive, which could make this step not needed. I could however not get this to work with external dependencies, which were located in the global Derived Data folder, which caused the build to fail.

Finally the `docc_platform` lane renames `SwiftUIKit.doccarchive` by adding a `values[:name]` suffix. This will cause the file to be named `SwiftUIKit_ios.doccarchive` for iOS etc.


### Step 2: Generate documentation archives for all platforms

To generate documentation archives for all supported platform, let's add a second lane called `docc`:

```sh
desc "Build documentation for all platforms"
lane :docc do
  sh('cd .. && rm -rf Docs')
  docc_platform(destination: 'iOS', name: 'ios')
  docc_platform(destination: 'OS X', name: 'osx')
  docc_platform(destination: 'tvOS', name: 'tvos')
  docc_platform(destination: 'watchOS', name: 'watchos')
end
```

It first deletes the local `Docs` folder to make us end up with a fresh one, then calls `docc_platform` to generate a documentation archive for each platform.


### Step 3: Generate a platform-specific static documentation website

With the platform-specific archives in place, we can now generate a static site for a specific platform:

```sh
desc "Build static documentation website for a single platform"
lane :docc_web_platform do |values|
  sh('cd .. && $(xcrun --find docc) process-archive \
    transform-for-static-hosting Docs/SwiftUIKit_' + values[:name] + '.doccarchive \
    --output-path Docs/web_' + values[:name] + ' \
    --hosting-base-path SwiftUIKit')
end
```

This lane calls `xcrun --find docc` and process the archive for the provided `values[:name]` to generate a static website in e.g. `Docs/web_ios`.


### Step 4: Generate static documentation websites for all platforms

To generate static documentation websites for all supported platform, let's add a lane called `docc_web`:

```sh
desc "Build static documentation websites for all platforms"
lane :docc_web do
  docc
  docc_web_platform(name: 'ios')
  docc_web_platform(name: 'osx')
  docc_web_platform(name: 'tvos')
  docc_web_platform(name: 'watchos')
end
```

This will first run `docc` to generate all documentation archives, then run `docc_web_platform` for each platform.


### Step 5: Preview documentation website

While we can probably use `$(xcrun --find docc)` to preview the online documentation, I haven't looked into this yet. 

I instead have specific lanes for this, that use the DocC plugin as described earlier:

```sh
desc "Build static web documentation (macOS only)"
lane :docc_web_plugin do
  sh('cd .. && mkdir -p Docs')
  sh('cd .. && swift package \
    --allow-writing-to-directory Docs \
    generate-documentation \
    --disable-indexing \
    --transform-for-static-hosting \
    --hosting-base-path SwiftUIKit \
    --output-path Docs/web')
end

desc "Build and preview static documentation website (macOS only)"
lane :docc_webpreview_plugin do
  sh('cd .. && mkdir -p Docs')
  sh('cd .. && swift package \
    --disable-sandbox \
    preview-documentation \
    --transform-for-static-hosting \
    --hosting-base-path SwiftUIKit \
    --output-path Docs/web')
end
```

This will generate macOS specific documentations, but since this is just for me to preview articles and type headers, it will do for now.


## Conclusion

I really like the DocC plugin, but it's currently not covering all my use-cases. I hope that it evolves to provide more options in the future. If so, I will probably adjust my lanes to use it more.

I'd also love to combine the static sites into a single one, but since each is around ~300MB for SwiftUIKit (how is this possible), I will just publish the iOS site and have it mention how to generate documentation for the other platforms from Xcode.

If you have the same challenges as I currently have, I hope that this post helped you out.