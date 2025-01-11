---
title:  Building multi-platform documentation with DocC
date:   2022-04-27 07:00:00 +0100
tags:   swift docc multi-platform

assets: /assets/blog/22/0427/
image:  /assets/blog/22/0427/image.jpg
tweet:  https://twitter.com/danielsaidi/status/1519332230535000067?s=20&t=wF1kbk5Nxm27t6vxQ1OeLQ

docc: https://developer.apple.com/documentation/docc
actions:  /blog/2024/03/10/automatically-publish-docc-to-github-pages-with-github-actions
fastlane: https://fastlane.tools
---

DocC is an amazing documentation tool for Swift-based projects. This post shows how to generate multi-platform documentation with DocC, using Terminal scripts and Fastlane.

{% include kankoda/data/open-source name="SwiftUIKit" %}

![DocC icon]({{page.image}})

This post assumes that you are familiar with Swift Packages and DocC. If not, you can look at [the DocC website]({{page.docc}}) for more information and [SwiftUIKit]({{project.url}}) for an example package.


## The documentation catalog

You can add DocC documentation to a Swift Package by adding a `Documentation Catalog`:

![Xcode - add documentation catalog]({{page.assets}}xcode.jpg)

The catalog should have the same name as your package and have a Markdown file with the same name in the root. For SwiftUIKit, it looks like this:

![SwiftUIKit documentation catalog]({{page.assets}}xcode-swiftuikit.jpg)

Whenever you generate documentation, DocC will use this file as the start page, which can be used to link to the types in the library, additional articles and tutorials etc.


## Generate documentation from Xcode

Xcode can build a `Documentation Archive` from your documentation catalog, which you can trigger with the `Product > Build Documentation` command.

For multi-platform packages, you can select which platform to generate documentation for. The generated archive will then be specific to that particular platform.


## Generate documentation from the Terminal

The script to generate documentation for a Swift Package from the Terminal is very basic:

```sh
xcodebuild docbuild \
    -scheme SwiftUIKit \
    -destination 'generic/platform=ios'
```

This will generate a documentation archive for iOS in Derived Data. There are a bunch of options, but this is the most basic way to do it.

You can replace `ios` with `OS X`, `tvOS`, `watchOS` & `xrOS` to target other platforms as well. 

Once you have an archive, you can generate a static website that can be hosted on e.g. GitHub Pages:

```sh
$(xcrun --find docc) process-archive \
    transform-for-static-hosting PATH_TO_ARCHIVE \
    --output-path Docs/web \
    --hosting-base-path SwiftUIKit
```

This will generate a static website in `Docs/web/`, which you can add to a `gh-pages` branch and push to GitHub. You can also setup automatic builds with [GitHub Actions]({{page.actions}}).

While these scripts are super simple, there is a pretty new DocC plugin that makes things even easier. Let's take a look at how it works.


## Generate documentation using the DocC plugin

The DocC plugin can be added to a Swift Package by adding this dependency to it:

```swift
dependencies: [
    .package(url: "https://github.com/apple/swift-docc-plugin", from: "1.0.0"),
]
```

This lets you build documentation with `swift package` instead of `xcodebuild` and `xcrun --find docc`. For instance, you can generate a website without first generating an archive:

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

For SwiftUIKit, which supports all platforms, I'd love for the documentation to support all platforms, but atm I don't know how to bundle multiple documentations into one.

Since I have automated my workflow with Fastlane, I created a bunch of lanes that let me generate multi-platform documentation with a single command. Let's take a look.


## Generate multi-platform documentation with scripts and Fastlane

If you're not familiar with [Fastlane]({{page.fastlane}}), it's a build tool that can automate your development and release processes. I use it for all my apps and SDKs.

Let's extend Fastlane for my Swift packages with a bunch of lanes that let me generate DocC documentation archives and static web sites, using a single command if possible.

To avoid that the setup becomes too Fastlane-specific, I will use the `sh` function to call regular scripts that you could call from the Terminal as well, without involving Fastlane.

The final script will contain more logic than just calling the scripts as above. We have to locate generated archives, clean up stuff etc. so our lanes will be a bit more complex.


### Step 1: Generate a platform-specific documentation archive

First, let's create a `docc_platform` lane that generates documentation for a single platform:

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

The `sh` function actually executes in the `Fastlane` folder. This means that we have to add `cd .. &&` before all scripts to ensure that they are executed in the project root.

This script creates a `Docs` folder, if none exists. It then calls a `docc_delete_derived_data`, which looks like this:

```sh
desc "Delete documentation derived data (may be historic duplicates)"
lane :docc_delete_derived_data do
  sh('find ~/Library/Developer/Xcode/DerivedData \
    -name "SwiftUIKit.doccarchive" \
    -exec rm -Rf {} \; || true')
end
```

This locates and deletes all `SwiftUIKit.doccarchive` in the global Derived Data folder. This is needed since there may be many and we must have exactly one for later steps. `|| true` is added to silence any errors that will otherwise cause Fastlane to abort.

The `docc_platform` lane then runs `xcodebuild docbuild` to create a documentation archive for the platform specified in `values[:destination]`, which can be `ios`, `OS X` etc.

Once the archive is generated, the lane runs `find` to find the documentation archive within Derived Data and moves it to the local `Docs` folder.

You can specify a custom derived data folder, which could make this step obsolete. I could however not get this to work with external dependencies, which were located in the global Derived Data folder, which caused the build to fail.

The `docc_platform` lane finally renames the `SwiftUIKit.doccarchive` archive by adding a `values[:name]` suffix to it, which gives it a platform-specific name.


### Step 2: Generate documentation archives for all platforms

To generate documentation archives for all platform, let's add a second lane called `docc`:

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

It deletes the local `Docs` folder to setup a fresh one, then calls `docc_platform` to generate a documentation archive for each platform.


### Step 3: Generate a platform-specific static documentation website

With platform-specific archives in place, we can now create a static site for each platform:

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

To generate static websites for all supported platform, let's add a lane called `docc_web`:

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

This will run `docc` to generate all documentation archives, then run `docc_web_platform` for each platform.


### Step 5: Preview documentation website

While we can use `$(xcrun --find docc)` to preview the web documentation, I haven't used in yet. I instead have specific lanes for this, that use the DocC plugin:

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

**Update 2024-06-27:** I removed the DocC plugin dependency a long time ago. Have a look at any of my [open-source projects](/opensource) for an updated Fastlane setup.


## Conclusion

I really like the DocC plugin, but it's currently not covering all my needs. I hope it evolves to provide more options in the future. If so, I will probably adjust my lanes to use it more.

I'd also love to combine the static sites into a single one, but since each is around ~300MB for SwiftUIKit (how is this possible!?), I will publish the iOS site and have it mention how to generate documentation for the other platforms from Xcode.