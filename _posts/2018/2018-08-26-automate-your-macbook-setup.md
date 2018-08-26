---
title:  Automate your MacBook setup
date:   2018-08-26 17:28:01 +0200
tags:	osx macbook homebrew rubygems npm automation
---

In this post, I'll describe how you can automate setting up a new MacBook using
a modular and extensible terminal script. The original post can be found [here](https://medium.com/@danielsaidi/automate-your-macbook-setup-297e9cf3d148).


## Why automate?

I (and many system developers with me) prefer to automate as many parts of the
development process as possible. Some reasons for this are to reduce the amount
of repetitive, manual work (which quickly becomes tedious), reduce the
risk of human error and increase the reliability of the entire process, including
testing, continous integration, release management etc.

One thing that I however did NOT automate until recently, was to setup a new
MacBook for development. Doing this manually is time consuming, tiresome, error
prone and just not fun. It's also tedious to remember all the tolls and apps
that you need. Without automation, you'll be filling out gaps for weeks.

With automation, however, you can setup a new MacBook within minutes, basically
making the setup time linear to the speed of your Internet connection. I still
have to install some apps manually, like Xcode and apps from the Mac App Store,
but that's nothing compared to before.


## Tools on which I base my script

Before I show you the script that I have put together to solve this problem, let
me first go through some of the tools that I base the script on. These tools are
great and will simplify your life. I really recommend you to check them out.

* [Homebrew](https://brew.sh) is a package manager (one of several) for OSX. It
makes it super easy to install new system tools on your Mac.

* [Homebrew Cask](https://github.com/Homebrew/homebrew-cask) is an extension to
Homebrew, that lets you install applications directly from the Terminal.

* [Gem](https://rubygems.org/pages/download) (or RubyGems) is another package
manager for Ruby. It's used to install Ruby-based software, e.g. Fastlane.

* [NPM](https://www.npmjs.com) is (yet) another package manager for Node. It's
mainly used for web-related software, but I also use it for various hybrid app
libraries.

My script will install these tools as the very first thing it does. I will then
be able to install everything else, using these tools.


## Creating the script

I will now create the script that will install and setup everything I need. The
script will be modular and extensive, to make it easy to add or remove things as
my needs change over time.

First, create a file called `setup.sh` and add the following code to it:

```bash
#!/bin/bash

while true; do
  if [[ $# == 0 ]]; then
    printf "\n\n***** Setup *****\n\n"

    printf "Available commands:\n\n"
    printf "      all:  Install everything\n"
    printf "\n"
    printf "     apps:  Install applications\n"
    printf "   config:  Configure OS X\n"
    printf " fastlane:  Install Fastlane\n"
    printf "      ssh:  Create & copy SSH key\n"
    printf "   system:  Install system software\n"
    printf "      npm:  Install npm packages\n"
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

This script prints a "main menu" with various options. As you can see, I have
divided the setup into system setup, configuration, app installation etc.

The script lacks a `process_options` function, which it refers to. Add this code
above `while true`:


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

As you can see, processing a menu option basically just calls another script in
a `scripts` folder. The only exception is `all`, which calls all scripts.

Each file is then super simple; it's basically just a copy/paste of commands you
type manually in the terminal. Have a look at a few examples from each file:


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
```

### scripts/config.sh

```bash
printf "[CONFIG] Finder, Show hidden files\n"
defaults write com.apple.finder AppleShowAllFiles -bool true
killall Finder -9
printf "\n"
```

### scripts/apps.sh

```bash
printf "[APPS] Installing Android Studio\n"
brew cask install android-studio
printf "\n"
```

### scripts/npm.sh

```bash
printf "[NPM] Installing TypeScript\n"
sudo npm install -g typescript
printf "\n"
```

### scripts/fastlane.sh

```bash
printf "[FASTLANE] Install Fastlane\n"
sudo gem install fastlane 
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


## Result

Once my version of the script has finished processing (with some `sudo`s), it
will have taken care of the following:

* Configuring OS X
* Installing package managers (Homebrew, Cask, RubyGems, NPM)
* Installing system tools (CocoaPods, Carthage, SwiftGen, SwiftLint etc.)
* Installing applications (Android Studio, Chrome, Slack etc.)
* Installing Fastlane Tools
* Installing NPM packages (Ionic, Gulp, TypeScript etc.)
* Setting up SSH (create a key, add to ssh-agent, copy to pasteboard)

Doing this manually would take me a couple of hours each and every time I had to
setup a MacBook from scratch. Now, the script finishes in a matter of minutes.


## Download

If you don't want to start from scratch, I have a GitHub repo that you can fork
and tweak to fit your needs. You can find it [here](https://github.com/danielsaidi/osx)

This script is easy to configure. You can add and remove things as you see fit.
Hopefully, it will save you a lot of time.

All the best

Daniel
