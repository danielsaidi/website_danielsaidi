---
title:  Replacing Fastlane with script files
date:   2024-10-03 06:00:00 +0000
tags:   ci/cd github

image:  /assets/blog/24/1003.jpg
image-show: 0

post_ghpages: /blog/2024/03/10/automatically-publish-docc-to-github-pages-with-github-actions
sdk: https://github.com/danielsaidi/SwiftPackageBuildScripts
---

After many, many years of great service, I'm looking to phase out Fastlane and replace it with plain Shell script files, which I hope will result in faster builds and...less Ruby.

<!--![Header image]({{page.image}})-->

I use Fastlane to build, test and generate new versions of my various [open-source projects](/opensource). While the Fastlane setup has been quite complex earlier, the default one is pretty basic:

```bash
fastlane_version "2.129.0"

default_platform :ios

platform :ios do

  name = "EmojiKit"
  main_branch = "main"


  # Build ==================

  lane :build do |options|
    build_platform(platform: "iOS")
    build_platform(platform: "OS X")
    build_platform(platform: "tvOS")
    build_platform(platform: "watchOS")
    build_platform(platform: "xrOS")
  end

  lane :build_platform do |options|
    platform = options[:platform]
    sh("cd .. && xcodebuild -scheme " + name + " -derivedDataPath .build -destination 'generic/platform=" + platform + "';")
  end


  # Test ==================

  lane :test do
    test_platform(platform: "platform=iOS Simulator,name=iPhone 16")
  end

  lane :test_platform do |options|
    platform = options[:platform]
    sh("cd .. && xcodebuild test -scheme " + name + " -derivedDataPath .build -destination '" + platform + "' -enableCodeCoverage YES;")
  end


  # Version ================

  desc "Create a new version"
  lane :version do |options|
    version_validate
    version_build

    type = options[:type]
    version = version_bump_podspec(path: 'Version', bump_type: type)
    git_commit(path: "*", message: "Bump to #{version}")
    add_git_tag(tag: version)
    push_git_tags()
    push_to_git_remote()
  end
  
  desc "Validate that the SDK is ready for release"
  lane :version_validate do
    ensure_git_status_clean
    ensure_git_branch(branch: main_branch)
    swiftlint(strict: true)
  end
  
  desc "Validate that the repo is valid for release"
  lane :version_build do
    build
    test
  end

end
```

I also used to have DocC (Apple's documentation tool for Swift software) scripts in here, but moved that part to a GitHub workflow, as described in [this post]({{page.post_ghpages}}).


## DocC script and GitHub workflow

The `.github/workflows/docc.yml` workflow that makes GitHub Actions publish DocC documentation to GitHub pages looks like this:

```bash
name: DocC Runner

on:
  push:
    branches: ["main"]

env:
  SCHEME: EmojiKit

# Sets permissions of the GITHUB_TOKEN to allow deployment to GitHub Pages
permissions:
  contents: read
  pages: write
  id-token: write

# Allow one concurrent deployment
concurrency:
  group: "pages"
  cancel-in-progress: true
  
jobs:
  deploy:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: macos-15
    steps:
      - name: Checkout
        uses: actions/checkout@v3
      - id: pages
        name: Setup Pages
        uses: actions/configure-pages@v4
      - name: Select Xcode 16
        uses: maxim-lobanov/setup-xcode@v1
        with:
          xcode-version: '16.0'
      - name: Build DocC
        run: bash scripts/docc.sh $SCHEME
      - name: Upload artifact
        uses: actions/upload-pages-artifact@v3
        with:
          path: '.build/docs'
      - id: deployment
        name: Deploy to GitHub Pages
        uses: actions/deploy-pages@v4
```

As you can see in the `Build DocC` step, it now runs an external script file instead of having it inline. This makes the workflow file a lot smaller and easier to overview.

The `scripts/docc.sh` script script file looks like this:

```bash
#!/bin/bash

# Build DocC documentation for a <TARGET> to .build/docs.

# USAGE: bash scripts/docc.sh <TARGET>

TARGET=$1
TARGET_LOWERCASED=$(echo "$1" | tr '[:upper:]' '[:lower:]')

swift package resolve;

xcodebuild docbuild -scheme $1 -derivedDataPath /tmp/docbuild -destination 'generic/platform=iOS';

$(xcrun --find docc) process-archive \
transform-for-static-hosting /tmp/docbuild/Build/Products/Debug-iphoneos/$1.doccarchive \
--output-path .build/docs \
--hosting-base-path '$1';

echo "<script>window.location.href += \"/documentation/$TARGET_LOWERCASED\"</script>" > .build/docs/index.html;
```


## Build scripts

To avoid having to use Fastlane to build the SDK, let's convert these `Fastfile` lanes to script files:

```ruby
lane :build do |options|
  build_platform(platform: "iOS")
  build_platform(platform: "OS X")
  build_platform(platform: "tvOS")
  build_platform(platform: "watchOS")
  build_platform(platform: "xrOS")
end

lane :build_platform do |options|
  platform = options[:platform]
  sh("cd .. && xcodebuild -scheme " + name + " -derivedDataPath .build -destination 'generic/platform=" + platform + "';")
end
```

Let's first create a `scripts/build_framework.sh` script to replace the `build_platform` lane. It just takes the code from inside `sh(...)`, removes the `cd ..` and rewrites it a little:

```bash
#!/bin/bash

# Build a <TARGET> for a specific platform.

# USAGE: bash scripts/build_platform.sh <TARGET> <PLATFORM>

TARGET=$1
PLATFORM=$2

xcodebuild -scheme $TARGET -derivedDataPath .build -destination generic/platform=$PLATFORM
```

We can then create a build script that replaces the `build` lane, that builds a target for all platforms.

This will be a bit more complicated, since the `build` script will use the `build_framework` script, which involves finding the script folder, then making script executable, then calling it for all platforms.

The resulting `scripts/build.sh` script file looks like this:

```bash
#!/bin/bash

# Build a <TARGET> for all supported platforms.

# USAGE: bash scripts/build.sh <TARGET>

# Exit immediately if a command exits with a non-zero status
set -e

# Create local argument variables.
TARGET=$1

# Check if a target is provided
if [ $# -eq 0 ]; then
    echo "Error: No target specified"
    exit 1
fi

# Use the script folder to refer to the platform script.
FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
SCRIPT="$FOLDER/build_platform.sh"

# Make the script executable
chmod +x $SCRIPT

# A function that builds a specific platform
build_platform() {
    local platform=$1
    echo "Building for $platform..."
    if ! bash $SCRIPT $TARGET $platform; then
        echo "Failed to build $platform"
        return 1
    fi
    echo "Successfully built $platform"
}

# Array of platforms to build
platforms=("iOS" "macOS" "tvOS" "watchOS" "xrOS")

# Loop through platforms and build
for platform in "${platforms[@]}"; do
    if ! build_platform "$platform"; then
        exit 1
    fi
done

echo "All platforms built successfully!"
```

This file is a bit more complicated. It first verifies that we have passed in the required `TARGET`, then locates the `build_platform` script and prepares it, then loops over all platforms.

The reason for having two files, while we could just use the one-line from `build_platform` within the loop, is that it's nice to be able to build a single platform with a single script.

With this in place, we can remove the `Fastfile` lanes and call the `build` script from `version_build`:

```ruby
fastlane_version "2.129.0"

default_platform :ios

platform :ios do

  name = "EmojiKit"
  main_branch = "main"


  # Test ==================

  lane :test do
    test_platform(platform: "platform=iOS Simulator,name=iPhone 16")
  end

  lane :test_platform do |options|
    platform = options[:platform]
    sh("cd .. && xcodebuild test -scheme " + name + " -derivedDataPath .build -destination '" + platform + "' -enableCodeCoverage YES;")
  end


  # Version ================

  desc "Create a new version"
  lane :version do |options|
    version_validate
    version_build

    type = options[:type]
    version = version_bump_podspec(path: 'Version', bump_type: type)
    git_commit(path: "*", message: "Bump to #{version}")
    add_git_tag(tag: version)
    push_git_tags()
    push_to_git_remote()
  end

  desc "Validate that the SDK is ready for release"
  lane :version_validate do
    ensure_git_status_clean
    ensure_git_branch(branch: main_branch)
    swiftlint(strict: true)
  end

  desc "Validate that the repo is valid for release"
  lane :version_build do
    sh("cd .. && bash scripts/build.sh " + name)
    test
  end

end
```


## Test scripts

To avoid having to use Fastlane to test the SDK, we can repeat the abov process for these lanes:

```ruby
lane :test do
  test_platform(platform: "platform=iOS Simulator,name=iPhone 16")
end

lane :test_platform do |options|
  platform = options[:platform]
  sh("cd .. && xcodebuild test -scheme " + name + " -derivedDataPath .build -destination '" + platform + "' -enableCodeCoverage YES;")
end
```

This is shorter, since I only run the unit tests for iOS. Feel free to add more platforms if you need to.

Let’s first create a `scripts/test_framework.sh` script to replace the `test_platform` lane. Just like with the build script, we can rewrite the code in `sh(...)`:

```bash
#!/bin/bash

# Test the SDK for a specific platform.

# Use _ instead of spaces when passing in the <PLATFORM>.

# USAGE: bash scripts/build_platform.sh <TARGET> <PLATFORM>

TARGET=$1
PLATFORM="${2//_/ }"

xcodebuild test -scheme $TARGET -derivedDataPath .build -destination "$PLATFORM" -enableCodeCoverage YES;
```

This file uses a workaround to handle spaces in the platform, since calling this file with spaces will cause the platform to be interpreted as multiple arguments.

We can now create a `scripts/test.sh` script that replaces the `test` lane. It will be as complicated as the `build` script, since it needs to work in the same way:

```bash
#!/bin/bash

# Test a <TARGET> for all supported platforms.

# USAGE: bash scripts/test.sh <TARGET>

# Exit immediately if a command exits with a non-zero status
set -e

# Create local argument variables.
TARGET=$1

# Check if a target is provided
if [ $# -eq 0 ]; then
    echo "Error: No target specified"
    exit 1
fi

# Use the script folder to refer to the platform script.
FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
SCRIPT="$FOLDER/test_platform.sh"

# Make the script executable
chmod +x $SCRIPT

# A function that builds a specific platform
test_platform() {
    local platform=$1
    echo "Building for $platform..."
    if ! bash $SCRIPT $TARGET $platform; then
        echo "Failed to build $platform"
        return 1
    fi
    echo "Successfully built $platform"
}

# Array of platforms to build
platforms=("platform=iOS_Simulator,name=iPhone_16")

# Loop through platforms and build
for platform in "${platforms[@]}"; do
    if ! test_platform "$platform"; then
        exit 1
    fi
done

echo "All platforms tested successfully!"
```

With this in place, we can remove the `Fastfile` lanes and call the `test` script from `version_build`:

```ruby
fastlane_version "2.129.0"

default_platform :ios

platform :ios do

  name = "EmojiKit"
  main_branch = "main"

  desc "Create a new version"
  lane :version do |options|
    version_validate
    version_build

    type = options[:type]
    version = version_bump_podspec(path: 'Version', bump_type: type)
    git_commit(path: "*", message: "Bump to #{version}")
    add_git_tag(tag: version)
    push_git_tags()
    push_to_git_remote()
  end

  desc "Validate that the SDK is ready for release"
  lane :version_validate do
    ensure_git_status_clean
    ensure_git_branch(branch: main_branch)
    swiftlint(strict: true)
  end

  desc "Validate that the repo is valid for release"
  lane :version_build do
    sh("cd .. && bash scripts/build.sh " + name)
    sh("cd .. && bash scripts/test.sh " + name)
  end

end
```


## Validation scripts

To be able to move even more things out of `Fastfile`, we must create Shell script variants for some convenient Fastlane scripts.

Let's begin with this `scripts/validate_git.sh` file, which replaces the `ensure_git_status_clean` and `ensure_git_branch(branch: main_branch)` steps with a single step:

```bash
#!/bin/bash

# Validate the Git repository for an optional <BRANCH>.

# USAGE: bash scripts/validate_git.sh <BRANCH>

# Create local argument variables.
BRANCH=$1

# Check if a branch is provided
if [ $# -eq 0 ]; then
    echo "Error: No branch specified"
    exit 1
fi

# Check if the current directory is a Git repository
if ! git rev-parse --is-inside-work-tree > /dev/null 2>&1; then
    echo "Error: Not a Git repository"
    exit 1
fi

# Check for uncommitted changes
if ! git diff-index --quiet HEAD --; then
    echo "Error: Git repository is dirty. There are uncommitted changes."
    exit 1
fi

# If a branch name is provided, check if we're on that branch
if [ $# -eq 1 ]; then
    current_branch=$(git rev-parse --abbrev-ref HEAD)
    if [ "$current_branch" != "$1" ]; then
        echo "Error: Not on the specified branch. Current branch is $current_branch, expected $1."
        exit 1
    fi
    echo "Git repository is clean and on the correct branch ($1)."
elif [ $# -gt 1 ]; then
    print_usage
    exit 1
fi

# The Git repository validation succeeded.
exit 0
```

The script uses `git rev-parse` to check that we're in a git repo, then `git diff-index` to check if we're on `HEAD`, then finally uses `git rev-parse` again to check if we're on the correct branch. 

We can then create a `validate_project.sh` script that performs other quality checks, like linting:

```bash
#!/bin/bash

# Validate the project.

# USAGE: bash scripts/validate_project.sh

swiftlint
# ...add other things here
```

We can now replace the two `ensure` scripts with `validate_git` and `validate_project`. And since we have scripts, we can remove the `version_build` lane and move the build and test steps into `version`:

```ruby
fastlane_version "2.129.0"

default_platform :ios

platform :ios do

  name = "EmojiKit"
  main_branch = "main"

  desc "Create a new version"
  lane :version do |options|
    sh("cd .. && bash scripts/validate_git.sh " + main_branch)
    sh("cd .. && bash scripts/validate_project.sh")
    sh("cd .. && bash scripts/build.sh " + name)
    sh("cd .. && bash scripts/test.sh " + name)

    type = options[:type]
    version = version_bump_podspec(path: 'Version', bump_type: type)
    git_commit(path: "*", message: "Bump to #{version}")
    add_git_tag(tag: version)
    push_git_tags()
    push_to_git_remote()
  end

end
```


## Version bump scripts

The next step is a little trickier, since we have to create a Shell script variant of the pretty awesome `version_bump_podspec` script, which bumps the current version and returns the new version number.

The script accepts a "bump type" which can be `major`, `minor`, `patch`, etc. This lets us bump a version in different ways, depending on the kind of version we want to create.

Although the script has the word `podspec` in its name, you use it with any Ruby file. For instance, my projects have a `Version` file that defines the version number like this:

```ruby
Version::Number.new do |v|
  v.version = '1.0.0'
end
```

Still...why do we need a separate version file to track the version number, when we already use git? We can remove this dependency to make things more flexible.

One common problem with the current setup, is that I can't use the version script to create release candidates from a feature branch, since the script won't let me create a `9.0-rc.1` version.

So, instead of keeping the `Version` and `version_bump_podspec`, I will use git to fetch the latest version number, then replace the bump script with a manual version number step.

Let's first add this `scripts/version.sh` script file, which returns the last semver conforming version:

```bash
#!/bin/bash

# Get the latest project version number from git.

# USAGE: bash scripts/version.sh

# Check if the current directory is a Git repository
if ! git rev-parse --is-inside-work-tree > /dev/null 2>&1; then
    echo "Error: Not a Git repository"
    exit 1
fi

# Fetch all tags
git fetch --tags > /dev/null 2>&1

# Get the latest semver tag
VERSION=$(git tag -l --sort=-v:refname | grep -E '^v?[0-9]+\.[0-9]+\.[0-9]+$' | head -n 1)

# Check if we found a version tag
if [ -z "$VERSION" ]; then
    echo "Error: No semver tags found in this repository" >&2
    exit 1
fi

# Print the latest version
echo "$VERSION"
```

The file fetches all tags, then uses a regex to fetch the latest semantic version, for instance `1.2.3`. 

We can now create a `scripts/bump_version.sh` script, that calls the `version` script, prints the version number, then wait for the user to enter which version number (or name) to bump to:

```bash
#!/bin/bash

# Bump the project version number.

# USAGE: bash scripts/version_bump.sh

# Use the script folder to refer to the platform script.
FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
SCRIPT="$FOLDER/version.sh"

# Get the latest version
VERSION=$($SCRIPT)

# Function to validate semver format, including optional -rc.<INT> suffix
validate_semver() {
    if [[ $1 =~ ^v?[0-9]+\.[0-9]+\.[0-9]+(-rc\.[0-9]+)?$ ]]; then
        return 0
    else
        return 1
    fi
}

if [ $? -ne 0 ]; then
    echo "Failed to get the latest version"
    exit 1
fi

echo "The current version is: $VERSION"

# Prompt user for new version
while true; do
    read -p "Enter the new version number: " NEW_VERSION

    if validate_semver "$NEW_VERSION"; then
        break
    else
        echo "Invalid version format. Please use semver format (e.g., 1.2.3, v1.2.3, 1.2.3-rc.1, etc.)."
        exit 1
    fi
done

echo "$NEW_VERSION"
```

For now, the script only fetches the current version, prompts you about a new version number and returns the number you typed. That's not particularly useful.

The reason for pausing here, is that I want to take a step back and look at the current `Fastfile`:

```ruby
fastlane_version "2.129.0"

default_platform :ios

platform :ios do

  name = "EmojiKit"
  main_branch = "main"

  desc "Create a new version"
  lane :version do |options|
    sh("cd .. && bash scripts/validate_git.sh " + main_branch)
    sh("cd .. && bash scripts/validate_project.sh")
    sh("cd .. && bash scripts/build.sh " + name)
    sh("cd .. && bash scripts/test.sh " + name)

    type = options[:type]
    version = version_bump_podspec(path: 'Version', bump_type: type)
    git_commit(path: "*", message: "Bump to #{version}")
    add_git_tag(tag: version)
    push_git_tags()
    push_to_git_remote()
  end

end
```

We *could* replace the `type` and `version` lines with the script above, but we're actually ready to leave `Fastlane` behind, so let's instead create Shell script versions for the last four lines:

```bash
git commit -am "Bump to #$NEW_VERSION"
git tag $NEW_VERSION
git push --tags
git push -u origin HEAD
```

However, since we no longer update the version in the `Version` file, we can remove the `git commit`:

```bash
git tag $NEW_VERSION
git push --tags
git push -u origin HEAD
```

Add these three lines to the `version_bump.sh` and it now looks like this:

```bash
#!/bin/bash

# Bump the project version number.

# USAGE: bash scripts/version_bump.sh

# Use the script folder to refer to the platform script.
FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
SCRIPT="$FOLDER/version.sh"

# Get the latest version
VERSION=$($SCRIPT)

# Function to validate semver format, including optional -rc.<INT> suffix
validate_semver() {
    if [[ $1 =~ ^v?[0-9]+\.[0-9]+\.[0-9]+(-rc\.[0-9]+)?$ ]]; then
        return 0
    else
        return 1
    fi
}

if [ $? -ne 0 ]; then
    echo "Failed to get the latest version"
    exit 1
fi

echo "The current version is: $VERSION"

# Prompt user for new version
while true; do
    read -p "Enter the new version number: " NEW_VERSION

    if validate_semver "$NEW_VERSION"; then
        break
    else
        echo "Invalid version format. Please use semver format (e.g., 1.2.3, v1.2.3, 1.2.3-rc.1, etc.)."
        exit 1
    fi
done

git tag $NEW_VERSION
git push --tags
git push -u origin HEAD
```

That's all we need to replace the `version_bump_podspec` script with a more flexible alternative. As a bonus, we can also remove the `Version` file and `Fastlane` folder.


## Version creation scripts

The next step is to create a `scripts/version_create.sh` script that takes a target and git branch, then performs all the required validations before calling the `version_bump` script:

```swift
#!/bin/bash

# Create a new project version for the provided <BUILD_TARGET> and <GIT_BRANCH>.

# USAGE: bash scripts/version_create.sh <BUILD_TARGET> and <GIT_BRANCH>

# Exit immediately if a command exits with a non-zero status
set -e

# Check if both arguments are provided
if [ $# -ne 2 ]; then
    echo "Error: This script requires exactly two arguments"
    echo "Usage: $0 <BUILD_TARGET> <GIT_BRANCH>"
    exit 1
fi

# Create local argument variables.
BUILD_TARGET="$1"
GIT_BRANCH="$2"

# Use the script folder to refer to the platform script.
FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
VALIDATE_GIT="$FOLDER/validate_git.sh"
VALIDATE_PROJECT="$FOLDER/validate_project.sh"
BUILD="$FOLDER/build.sh"
TEST="$FOLDER/test.sh"
VERSION_BUMP="$FOLDER/version_bump.sh"

# A function that run a certain script and checks for errors
run_script() {
    local script="$1"
    shift # Remove the first argument (the script path)

    if [ ! -f "$script" ]; then
        echo "Error: Script not found: $script"
        exit 1
    fi

    chmod +x "$script"
    if ! "$script" "$@"; then
        echo "Error: Script $script failed"
        exit 1
    fi
}


# Execute the pipeline steps
echo "Starting pipeline for BUILD_TARGET: $BUILD_TARGET, GIT_BRANCH: $GIT_BRANCH"

echo "Validating Git..."
run_script "$VALIDATE_GIT" "$GIT_BRANCH"

echo "Validating Project..."
run_script "$VALIDATE_PROJECT"

echo "Building..."
run_script "$BUILD" "$BUILD_TARGET"

echo "Testing..."
run_script "$TEST" "$BUILD_TARGET"

echo "Bumping version..."
run_script "$VERSION_BUMP"

echo ""
echo "Version created successfully!"
echo ""
```

We are more explicit in our argument naming here, since this script is more general.

The script will call multiple scripts in sequence, to perform all the steps that we have created earlier. 

We can now create new versions with `bash scripts/version_create.sh EmojiKit main`. The script will validate git, validate the project, build and test, then finally bump the version if everything worked.

To avoid having to type the project name and branch every time we, we can create a project-specific script file in the project root, that does this for us. Let's name it `version_create.sh` as well:

```bash
SCRIPT="scripts/version_create.sh"
chmod +x $SCRIPT
chmod +x version_create.sh
bash $SCRIPT EmojiKit main
```

With this file in place, we can create new versions by just typing `bash version_create.sh`. And since the file makes itself executable, you can just type `./version_create.sh` for all subsequent versions.


## Conclusion

This became longer than I intended, but I'm happy that I actually managed to replace the entire Fastlane setup with Shell scripts, with some additional flexibility as a result.

If you want to try this out, I have published them at [this GitHub repo]({{page.sdk}}). You can just add the scripts to any Swift package to then easily build, test, and create new well-tested versions of your package.

I'd love to hear what you think of this approach, and if you think any of the scripts can be improved. Don't mention to reach out in the comment section below, or comment on social media, using the links below.

Thank you for reading!