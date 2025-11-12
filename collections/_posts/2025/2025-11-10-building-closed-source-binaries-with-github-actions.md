---
title:  Building Closed-Source Binaries with GitHub Actions
date:   2025-11-09 06:00:00 +0000
tags:   swift sdks automation

assets: /assets/blog/25/1109/
image:  /assets/blog/25/1109/image.jpg
image-show: 0

talk:   /talks/2025/05/12/distribute-and-monetize-closed-source-sdks-with-the-swift-package-manager
github-article: https://docs.github.com/en/actions/how-tos/deploy/deploy-to-third-party-platforms/sign-xcode-applications
post:   /blog/2025/10/19/adding-dsyms-from-a-closed-source-sdk-to-your-app
scripts: https://github.com/danielsaidi/swiftpackagescripts

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3m57ish4b6c2g
toot: https://mastodon.social/@danielsaidi/115520617853324518
linkedin: https://www.linkedin.com/posts/danielsaidi_building-closed-source-binaries-with-github-activity-7393319947101159424-iylF
---

Using GitHub Actions is a great way to automate your build pipeline. In this post, we'll take a look at how to use GitHub Actions to build distribution binaries for a closed-source Swift package.


## Why using a cloud service to build distribution binaries?

Before we start looking at *how* to use GitHub Actions to build your distribution binaries, let's discuss *why* you may want to consider this instead of a local build setup.

Building the binaries with a cloud service like GitHub Actions lets you decouple your release process from your local environment. It also reduces the risk of malware affecting the build.

You naturally don't have to use GitHub Actions for this. You can use Xcode Cloud or another service with similar capabilities. While you'd have to adjust the workflow, most of the steps below still apply.


## How to set up a closed-source Swift package

Setting up a closed-source Swift package involves a few steps. The details are beyond the scope of this post, but you can watch my talk from [iOSKonf 25]({{page.talk}}) for more information.

Basically, you'd have to set up a *public* distribution repository, and a *private* source code repository. I also have separate repositories for build binaries and for the online documentation.

When releasing a new version of a closed-source package, the private source code is compiled to an XCFramework. You can also [generate dSYMs for each release]({{page.post}}) to enable symbolicated crash logs.

We'll not take a look at how we can use GitHub Actions to build the XCFramework and dSYMs for us.


## Step 1 - Generating code signing certificates

If you currenly make your binary builds locally, you may have to configure your closed-source Xcode project to use manual code signing instead of automatic, to let us archive with GitHub Actions.

To do this, toggle off the "Automatically manage signing" checkbox and select "Apple Distribution" as the signing certificate, with the Signing Certificate picker. 

![A screenshot of how to change Xcode code signing from Automatic to Manual]({{page.assets}}xcode-manual-signing.jpg)

If your project supports multiple platforms, you must set a distribution certificate for each platform. In this case, we can just use the same certificate for all platforms:

![A screenshot of how to change Xcode code signing for all platforms from Automatic to Manual]({{page.assets}}xcode-manual-signing-multiplatform.jpg)

When you select "Apple Distribution" for the first time, Xcode will open the certificate generator and guide you through all the steps. Make sure that you generate a *distribution certificate*.

Once the certificate is generated, you can right-click the certificate file and export it as a `.p12` file. You can export it anywhere, for instance to the Desktop:

![A screenshot of the exported p12 file]({{page.assets}}finder-exported.jpg)

If you have to enter a password when exporting the distribution certificate, it's very important that you remember it, since we will need it later.


## Step 2 - Setting up GitHub repository secrets

[This article]({{page.github-article}}) describes how to set up the repository secrets that are required to build the distribution binaries. We just need the Base64 `.p12` file content, the `.p12` file secret, and a keychain password:

![A screenshot of the GitHub secrets page]({{page.assets}}github-settings.jpg)

These secrets will be used by the workflow to allow GitHub Actions to archive the source-code with proper code signing.


## Step 3 - Setting up build scripts

Even though GitHub Actions makes it easy to automate build processes, you still need build scripts.

I have [an open-source project]({{page.scripts}}) that provides various scripts for open-source and closed-source Swift packages, as well as GitHub Actions workflows templates for common tasks.

Our workflow will use the `validate-release` script to ensure that the code is ready for release, and the `framework` script to generate the binaries. Have a look at [SwiftPackageScripts]({{page.scripts}}) for more scripts.

In the workflow below, we'll assume that the required build scripts are in the `/scripts` root folder.


## Step 4 - Setting up the GitHub Actions workflow

To set up the GitHub Actions workflow that will be used to build our distribution binaries, create a `.github/workflows` folder in the project root and add a `xcframework-binaries.yml` file to it.

Since the [GitHub article]({{page.github-article}}) doesn't include the build script used to generate the required binaries, the workflow will use [SwiftPackageScripts]({{page.scripts}})'s `framework.sh` script.

First, add this to the workflow file to give it a name, specify that we will trigger it manually, and that it should run on macOS 15. We also specify the framework name as a variable.


```yml
name: Create Binary Artifacts

on:
  workflow_dispatch:  # Manual trigger
  # Or add other triggers like:
  # push:
  #   tags:
  #     - 'v*'

jobs:
  build:
    runs-on: macos-15 # macos-latest
    env:
      FRAMEWORK_NAME: MyPackageName
```

When this was written, `macos-latest` didn't have tvOS, watchOS, or visionOS runtimes. We therefore tell the workflow to run on `macos-15`. When you read this `macos-latest` will most likely work.

Let's now add a build step that checks out the code, and one that uses the repository secrets to set up the distribution certificate and add it to the keychain:

```yml
    steps:
      - name: Check out code
        uses: actions/checkout@v4

      - name: Set up certificate
        env:
          BUILD_CERTIFICATE_BASE64: {% raw %}${{ secrets.BUILD_CERTIFICATE_BASE64 }}{% endraw %}
          P12_PASSWORD: {% raw %}${{ secrets.P12_PASSWORD }}{% endraw %}
          KEYCHAIN_PASSWORD: {% raw %}${{ secrets.KEYCHAIN_PASSWORD }}{% endraw %}
        run: |
          # create variables
          CERTIFICATE_PATH=$RUNNER_TEMP/build_certificate.p12
          KEYCHAIN_PATH=$RUNNER_TEMP/app-signing.keychain-db

          # import certificate from secrets
          echo -n "$BUILD_CERTIFICATE_BASE64" | base64 --decode -o $CERTIFICATE_PATH

          # create temporary keychain
          security create-keychain -p "$KEYCHAIN_PASSWORD" $KEYCHAIN_PATH
          security set-keychain-settings -lut 21600 $KEYCHAIN_PATH
          security unlock-keychain -p "$KEYCHAIN_PASSWORD" $KEYCHAIN_PATH

          # import certificate to keychain
          security import $CERTIFICATE_PATH -P "$P12_PASSWORD" -A -t cert -f pkcs12 -k $KEYCHAIN_PATH
          security set-key-partition-list -S apple-tool:,apple: -k "$KEYCHAIN_PASSWORD" $KEYCHAIN_PATH
          security list-keychain -d user -s $KEYCHAIN_PATH
```

With the distribution certificate added to the keychain, we can now create the XCFramework binary.

Let's start. by setting up Xcode. Just like with macOS, we had to define `16.4` since the `latest-stable` didn't work when this was written. You will likely be able to use `latest-stable`:

```yml
      - name: Set up Xcode
        uses: maxim-lobanov/setup-xcode@v1
        with:
          xcode-version: 16.4 # latest-stable doesn't currently work
```

We can now call `validate_release.sh` to ensure that we're on the main branch, that the code builds, and that all unit tests pass. We also disable SwiftLint, since it's not available as a command line tool:

```yml
      - name: Validate Project
        run: ./scripts/validate_release.sh --swiftlint 0
```

We can then call `framework.sh` to generate the XCFramework binaries. We can pass in `-p/--platform iOS` to only build for iOS (up to you), `-dsyms 1` to generate dSYMS and `--zip 0` to skip the zip steps:


```yml
      - name: Generate distribution binaries
        run: ./scripts/framework.sh -p iOS --dsyms 1 --zip 0
```

We can skip the zip step since the XCFramework and dSYMs will be zipped when they are uploaded.

Once the artifacts are built, we can upload the XCFramework and dSYMs with these last two steps:

```yml
      - name: Upload XCFramework
        uses: actions/upload-artifact@v4
        with:
          name: {% raw %}${{ env.FRAMEWORK_NAME }}.xcframework{% endraw %}
          path: {% raw %}.build/${{ env.FRAMEWORK_NAME }}.xcframework{% endraw %}
          if-no-files-found: error

      - name: Upload dSYMs
        uses: actions/upload-artifact@v4
        with:
          name: {% raw %}${{ env.FRAMEWORK_NAME }}-dSYMs{% endraw %}
          path: .build/dSYMs
          if-no-files-found: error
```

That's it! We are finally ready to push this workflow file to GitHub and try it out to see that it works.


## Step 5 - Running the GitHub Actions workflow

We can now push the workflow to GitHub and run it from the `Actions` tab. The workflow was set up with a manual trigger, but you can use `on:` to define automated triggers.

When the workflow finishes, it outputs the XCFramework and dSYMs zip files with their checksums:

![A screenshot of the GitHub result screen]({{page.assets}}github-result.jpg)

You can download the zip files and upload them somewhere public, then use them to create a new version of your SDK. Your users can download the dSYMs to get symbolicated crash logs.



## Conclusion

GitHub Actions lets you automate the binary distribution file build process for a closed-source Swift package, and [SwiftPackageScripts]({{page.scripts}}) has a workflows and scripts to get up and running in no time. 

I personally find the GitHub Actions workflow format a bit confusing, so I hope you find this helpful.