---
title:  Automate setting up your Mac
date:   2018-08-26 17:28:01 +0200
tags:	osx macOS macbook homebrew rubygems npm automation
redirect_from: 
  - /blog/2018/2018-08-26-automate-your-macbook-setup/
---

In this post, I'll describe how you can automate setting up your new Mac using a
modular and extensible terminal script. The original post can be found [here](https://medium.com/@danielsaidi/automate-your-macbook-setup-297e9cf3d148).


## Why automate?

I (and many developers with me) prefer to automate as many parts of a development
process as possible. Some reasons are to reduce the amount of repetitive, manual
work (which quickly becomes tedious), reduce the risk of human error and increase
the reliability of the entire process, including testing, continous integration,
release management etc.

However, one thing that I did NOT automate until recently, was to setup a brand
new Mac for development. Doing this manually is time consuming, tiresome, error
prone and just not fun. It's also tedious to remember all the tools and apps you
need. Without automation, you'll be filling out gaps for weeks.

With automation, however, you can setup a brand new Mac within minutes, basically
making the setup time linear to the speed of your Internet connection. I still
have to install apps from the Mac AppStore manually, but that's nothing compared
to all the work I had to do before automating.


## Tools on which I base my script

Before I show you the script that I have put together to solve this problem, let
me first go through some of the tools that I base the script on. These tools are
great and will simplify your life. I really recommend you to check them out.

* [Homebrew](https://brew.sh) is a package manager (one of several) for macOS.
It makes it super easy to install new system tools on your Mac.

* [Homebrew Cask](https://github.com/Homebrew/homebrew-cask) is a brew extension
that lets you install Mac applications directly from the Terminal.

* [Brew Bundle](https://github.com/Homebrew/homebrew-bundle) is a brew extension
that lets you manage brew and cask packages with a `Brewfile`.

* [Gem](https://rubygems.org/pages/download) is a package manager for Ruby-based
software, e.g. Fastlane.

* [NPM](https://www.npmjs.com) is a package manager for web-development software.
I also use it for hybrid apps.

My script will first install these tools, then install everything else using the
tools as well as other scripts.


## Creating the script

I will now create the main system script. The script will be modular, to make it
easy to adjust as my needs change over time.

First, create a file called `setup.sh` and add the following code to it:

```bash
#!/bin/bash

while true; do
  if [[ $# == 0 ]]; then
    printf "\n\n***** Setup *****\n\n"

    printf "Available commands:\n\n"
    printf "\n"
    printf "      all:  Install everything\n"
    printf "     brew:  Install packages & apps from Brewfile\n"
    printf "   config:  Configure macOS\n"
    printf "      gem:  Install packages from Gemfile\n"
    printf "      npm:  Install npm packages from scripts/npm.sh\n"
    printf "      ssh:  Create & copy SSH key\n"
    printf "   system:  Install system software\n"
    printf "\n"
    printf "        q:  Quit/Exit.\n"
    printf "\n\n"

    read -p "Enter option: " response
    printf "\n"
    process_option $response
  else
    process_option $1
  fi
done
```

This prints a "main menu" with various options. As you can see, I have split the
setup into several modules, with an `all` option that installs everything.

The script above lacks the `process_options` function that it refers to. Add the
following code snippet above `while true`:


```bash
process_option() {
  case $1 in
    'all')
      source scripts/config.sh
      source scripts/system.sh
      source scripts/apps.sh
      source scripts/npm.sh
      source scripts/fastlane.sh
      source scripts/ssh.sh
      break;;
    'apps')
      source scripts/apps.sh
      break;;
    'config')
      source scripts/config.sh
      break;;
    'fastlane')
      source scripts/fastlane.sh
      break;;
    'npm')
      source scripts/npm.sh
      break;;
    'ssh')
      source scripts/ssh.sh
      break;;
    'system')
      source scripts/system.sh
      break;;
      
    'q')
      break;;
    *)
      break;;
  esac
}
```

As you can see, each option basically just calls another script in the `scripts`
folder or runs a terminal command.

Each file is then super simple. `Brewfile` just contains a list of brew and cash
dependencies, `Gemfile` contains a list of gem dependencies and all files in the
`scripts` folder basically just contains commands you could type manually in the
terminal. Have a look at some examples from each file:


### scripts/system.sh

```bash
if ! command -v brew > /dev/null; then
    printf "[SYSTEM] Install Homebrew\n"
    ruby -e "$(curl --location --fail --silent --show-error https://raw.githubusercontent.com/Homebrew/install/master/install)"
else
    printf "[SYSTEM] Update Homebrew\n"
    brew update
fi
printf "\n"

printf "[SYSTEM] Install Cask\n"
brew tap caskroom/cask
printf "\n"

...
```

### scripts/config.sh

```bash
printf "[CONFIG] Finder, Show hidden files\n"
defaults write com.apple.finder AppleShowAllFiles -bool true
killall Finder -9
printf "\n"
```


### scripts/npm.sh

```bash
printf "[NPM] Installing TypeScript\n"
sudo npm install -g typescript
printf "\n"
```


### scripts/ssh.sh

```bash
read -p "[SSH] Create new SSH key (yes/no): " response
if test "$response" = "yes"; then
	printf "\n"
	read -p "Enter your e-mail: " ssh_email
	printf "\n"
    printf "[SSH] Creating ssh key\n"
    ssh-keygen -t rsa -b 4096 -C $ssh_email
fi
printf "\n"

printf "[SSH] Adding ssh key to ssh-agent\n"
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_rsa
printf "\n"

printf "[SSH] Copying ssh key to pasteboard\n"
pbcopy < ~/.ssh/id_rsa.pub

printf "[SSH] Done\n"
printf "\n"
```


### Brewfile

```bash
cask_args appdir: "/Applications"

brew "mas"
cask "android-studio"
...
```

### Gemfile

```bash
# frozen_string_literal: true
source "https://rubygems.org"
git_source(:github) {|repo_name| "https://github.com/#{repo_name}" }
gem 'cocoapods'
...
```


## Result

Once my version of the script has finished processing (with some `sudo`s), it
will have taken care of the following:

* Configuring OS X
* Installing package managers (Homebrew, Cask, RubyGems, NPM)
* Installing brew packages (Carthage, SwiftGen, SwiftLint etc.)
* Installing Mac applications (Chrome, Slack, Sketch etc.)
* Installing gem packages (CocoaPods, Fastlane, Jekyll etc.)
* Installing npm packages (Ionic, Gulp, TypeScript etc.)
* Setting up SSH (create a key, add to ssh-agent, copy to pasteboard)

Doing this manually would take me a couple of hours each and every time I had to
setup a new Mac from scratch. Now, the script finishes in a matter of minutes.


## Download

If you don't want to start from scratch, I have a GitHub repo that you can fork
and tweak to fit your needs. You can find it [here](https://github.com/danielsaidi/osx)

This script is easy to configure. You can add and remove things as you see fit.
Hopefully, it will save you a lot of time.

All the best

Daniel
