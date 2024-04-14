---
title:  How to automate DocC for a Swift Package with GitHub Actions
date:   2024-03-10 06:00:00 +0000
tags:   swift docc

image:  /assets/blog/2024/240310/title.jpg
assets: /assets/blog/2024/240310/

inspiration: https://maxxfrazer.medium.com/deploying-docc-with-github-actions-218c5ca6cad5

tweet:  https://x.com/danielsaidi/status/1766754008260104649?s=20
toot:   https://mastodon.social/@danielsaidi/112070621419993035
---

In this post, let's see how we can use GitHub Actions to automate building & publishing the DocC documentation for a Swift Package, each time we push to a specific branch.

![GitHub Actions Logo]({{page.assets}}header.jpg)

The workflow presented in this post is **heavily** inspired from [@maxxfrazer]({{page.inspiration}})'s Medium post, so if you like this, make sure to jump over there and give it a like.


## Background

I used to have DocC generation as part of my standard [open-source](/opensource) build workflow. Every new version bump required the code to pass linting, testing and DocC generation.

Every time a new version was successfully created, I would then take the generated docs and move them to another folder that pushed to the `gh-pages` branch.

Since the web docs become pretty huge (some are around 300MB), I also used `git amend` to only get a single commit for the `gh-pages` branch.

This has been tedious and error-prone, since each release has consisted of many manual steps, instead of one. Since I have many [projects](/opensource), the amount of manual work addded up.

I was therefore very happy to see that GitHub now lets you use GitHub Actions to publish new pages every time you push new changes. Let's see how it works.


## GitHub Settings

In your GitHub repository dashboard, go to `Settings` then select `Pages` in the side menu. 

![GitHub Settings]({{page.assets}}github.jpg)

Under `Build and deployment`, you can now select your GitHub Pages `Source`. Switch from `Deploy from a branch` to `GitHub Actions` to use GitHub Actions instead of a branch.


## DocC Publish Workflow

With this in place, I could delete my `gh-pages`, remove all DocC lanes from my `Fastfile` file, and replace it with this workflow, placed in a `.github/workflows/docc.yml` file:

```swift
name: DocC Runner

on:
  push:
    branches: ["main"]

# Sets permissions of the GITHUB_TOKEN to allow deployment to GitHub Pages
permissions:
  contents: read
  pages: write
  id-token: write

# Allow one concurrent deployment
concurrency:
  group: "pages"
  cancel-in-progress: true
  
# A single job that builds and deploys the DocC documentation
jobs:
  deploy:
    environment:
      name: github-pages
      url: {% raw %}${{ steps.deployment.outputs.page_url }}{% endraw %}
    runs-on: macos-13
    steps:
      - name: Checkout
        uses: actions/checkout@v4
      - name: Setup Pages
        uses: actions/configure-pages@v4
      - name: Select Xcode 15.1
        uses: maxim-lobanov/setup-xcode@v1
        with:
          xcode-version: '15.1.0'
      - name: Build DocC
        run: |
          swift package resolve;

          xcodebuild docbuild -scheme ApiKit -derivedDataPath /tmp/docbuild -destination 'generic/platform=iOS';
          
          $(xcrun --find docc) process-archive \
            transform-for-static-hosting /tmp/docbuild/Build/Products/Debug-iphoneos/ApiKit.doccarchive \
            --output-path docs \
            --hosting-base-path 'ApiKit';
          
          echo "<script>window.location.href += \"/documentation/apikit\"</script>" > docs/index.html;
      - name: Upload artifact
        uses: actions/upload-pages-artifact@v3
        with:
          path: 'docs'
      - id: deployment
        name: Deploy to GitHub Pages
        uses: actions/deploy-pages@v4
```

In the code above, we set up permissions for the GitHub token, make new pushes cancel any ongoing workflows, then start a job that builds and deploys the documentation.

The job sets up the environment, specifies that is should run on macOS 13 (I couldn't get 14 to work) and Xcode 15.1, then builds and uploads a new DocC build.

The DocC build step currently only builds for iOS, but you can add more `xcodebuild` rows to include more platforms. It adds a redirect to the root folder, then deploys the `doc` folder.

The DocC build step also write a JavaScript redirect into the root `index.html`, to redirect to the generated documentation. Without this, the root page would just show a blank page.

Compared to older posts that helped me get this in place, I've updated the required actions to the latest versions.


## Build Runner Workflow

Since I no longer build DocC for all platforms as part of a version bump, I now also use a `.github/workflows/build.yml` to build the package for all platform and run unit tests:

```swift
name: Build Runner

on:
  push:
    branches: ["main"]
  pull_request:
    branches: ["main"]
    
env:
  SCHEME: ApiKit

jobs:
  build:
    runs-on: macos-13
    steps:
      - uses: actions/checkout@v3
      - uses: maxim-lobanov/setup-xcode@v1
        with:
          xcode-version: '15.1.0'

      - name: Build iOS
        run: xcodebuild -scheme $SCHEME -derivedDataPath .build -destination 'generic/platform=iOS';
      - name: Build macOS
        run: xcodebuild -scheme $SCHEME -derivedDataPath .build -destination 'generic/platform=OS X';
      - name: Build tvOS
        run: xcodebuild -scheme $SCHEME -derivedDataPath .build -destination 'generic/platform=tvOS';
      - name: Build watchOS
        run: xcodebuild -scheme $SCHEME -derivedDataPath .build -destination 'generic/platform=watchOS';
      - name: Build visionOS
        run: xcodebuild -scheme $SCHEME -derivedDataPath .build -destination 'generic/platform=xrOS';

      - name: Test iOS
        run: xcodebuild test -scheme $SCHEME -derivedDataPath .build -destination 'platform=iOS Simulator,name=iPhone 15,OS=17.2' -enableCodeCoverage YES;
```

As you can see, this file has individual build steps for iOS, macOS, tvOS, watchOS and visionOS. It will make sure that the package builds for all supported platforms.

If your package only supports one or a few platforms, make sure to adjust the build steps to only include the platforms that your package supports.

I will expand this to run unit tests on all platforms as well, but it's a bit tricky to determine which device to run them on.


## Fastlane Version Bump Script

With the DocC and build runner workflows in place, we still need a build and test runner in the version bump script, to avoid faulty versions.

This is the new Fastlane file that I will use in all my open-source projects. It's basically just defining a version bump script (lane) that uses other lanes to ensure that the code is legit.

```swift
fastlane_version "2.129.0"

default_platform :ios


platform :ios do

  name = "ApiKit"
  main_branch = "main"


  # Build ==================
  
  lane :build do |options|
    platform = options[:platform]
    sh("cd .. && xcodebuild -scheme " + name + " -derivedDataPath .build -destination 'generic/platform=" + platform + "';")
  end
  
  lane :build_all do
    build(platform: "iOS")
    build(platform: "OS X")
    build(platform: "tvOS")
    build(platform: "watchOS")
    build(platform: "xrOS")
  end


  # Test ==================
  
  lane :test_ios do
    sh("cd .. && xcodebuild test -scheme " + name + " -derivedDataPath .build -destination 'platform=iOS Simulator,name=iPhone 15,OS=17.2' -enableCodeCoverage YES;")
  end


  # Version ================

  desc "Create a new version"
  lane :version do |options|
    version_validate

    type = options[:type]
    version = version_bump_podspec(path: 'Version', bump_type: type)
    git_commit(path: "*", message: "Bump to #{version}")
    add_git_tag(tag: version)
    push_git_tags()
    push_to_git_remote()
  end
  
  desc "Validate that the repo is valid for release"
  lane :version_validate do
    ensure_git_status_clean
    ensure_git_branch(branch: main_branch)
    swiftlint(strict: true)
    build_all
    test_ios
  end

end
```

It defines a `build` lane that build the package for any platform, as well as an iOS test lane.

The `version` lane calls `version_validate` to check that the git repo status is clean, that it's on the correct branch, and that `swiftlint` passes, then calls `build_all` and tests iOS.


## Conclusion

With these new GitHub workflows, I save a lot of time and a on not having to manually handle the DocC generation. Every new push to `main` does this for me.

I also get continuous build and test checks with the `build` workflow, that builds and tests the platform on every new push to `main`.

To conclude, I found GitHub Actions trickier to set up than e.g. Bitrise, but once in place, it integrates seemlessly with pull requests and other open-source workflows.

I'm currently working on migrating my various [open-source projects](/opensource) from my manual DocC workflow to this fully automated one. It will be very nice when it's all in place. 