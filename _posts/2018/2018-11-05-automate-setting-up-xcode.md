---
title:  Automate setting up Xcode
date:   2018-11-05 15:00:00 +0200
tags:	  macOS automation xcode fastlane homebrew swiftlint
image:  http://danielsaidi.com/assets/blog/xcode.png
---

In this post, I will show how you can automate setting up Xcode for you and your
team. I will descibe how you can setup critical development tools, enforce team
conventions and share code and text snippets, using `Homebrew` and `Fastlane` in
a way that is easy to extend later, whenever you need to automate more tasks.

<img src="/assets/blog/xcode.png" alt="Xcode icon" width="200"/>


## Why automate?

I (and many developers with me) prefer to automate as much as possible, to reduce
the amount of repetitive manual work, reduce the risk of human error and increase
the overall reliability of a certain task. For developers, this may often include
unit testing, continous integration, release management etc.

For a team, however, automation is also a way to simplify for developers to setup
their environment and to make it easy to follow common conventions. For instance,
you can use a `lint` tool to enforce code conventions in a way that not complying
to them cause warnings and errors. For text and comments that do not compile, but
still should follow team conventions, code snippets can help you remove a lot of
tedious and error prone manual work.


## Automate setting up required tools

`swiftlint` is a great tool that can be injected into the build setup, to trigger
warnings and errors if code doesn't follow certain conventions. The default setup
is pretty good, but you can customize it by adding a `.swiftlint.yml` file to the
project root, in which you can ignore some rules or tweak them to fit your style.

When you read the `swiftlint` readme, it suggests you to add a `Run Script Phase`
that looks like this:

```bash
if which swiftlint >/dev/null; then
    swiftlint
else
    echo "SwiftLint does not exist, download from https://github.com/realm/SwiftLint"
fi
```

However, since `swiftlint` is such a critical tool, I think that the optionality
is really bad. Instead, my build step looks just like this:

```bash
swiftlint
```

This means that the app now fails to build whenever `swiftlint` isn't available,
which means that `swiftlint` has gone from being an optional to a required tool.
Whenever you have such a setup, perhaps together with dependencies to a bunch of
other required tools, you must make it easy for your team to install these tools, 
preferably with a single command. To achieve this, we can add a `Brewfile` to the
project root and add all required tools to it, for instance:

```bash
brew "carthage"
brew "swiftgen"
brew "swiftlint" 
```

Your team can now install all tools by typing this command in the project root:

```bash
brew bundle
``` 

Although `homebrew` is a standard tool, you can always make this setup even more
accessible by adding it to `Fastlane`. Just add two new lanes to your `Fastfile`:

```
desc "Setup Xcode"
lane :setup_xcode do |options|
  setup_xcode_tools
end

desc "Setup Xcode Tools"
lane :setup_xcode_tools do |options|
  sh "cd .. && brew bundle"
end
```

Now, your team just have to type `fastlane setup_xcode` to install all tools. It
may seem like a no-win for now, but the nice thing with this approach is that it
can easily be extended to handle even more tasks, unlike `brew bundle`.



## Automate setting up custom Xcode snippets

Xcode snippets is a nice tool that makes it easy to auto generate code and text
that you type often, that should follow certain conventions. For instance, I use
snippets to generate `MARK` comments, extension bodies, test suite imports, test
suite bodies etc. They save me a lot of time and make conforming to conventions
effortless.

For instance, a snippet that creates a "plain" `// MARK - ` statement is defined
in a file named `mark_plain.codesnippet`, that looks like this:

```
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>IDECodeSnippetCompletionPrefix</key>
  <string>mark_plain</string>
  <key>IDECodeSnippetCompletionScopes</key>
  <array>
    <string>All</string>
  </array>
  <key>IDECodeSnippetContents</key>
  <string>// MARK: - </string>
  <key>IDECodeSnippetIdentifier</key>
  <string>6A7BC3C9-32A4-4EE8-A8DC-7848BC0E40F3</string>
  <key>IDECodeSnippetLanguage</key>
  <string>Xcode.SourceCodeLanguage.Swift</string>
  <key>IDECodeSnippetTitle</key>
  <string>Mark</string>
  <key>IDECodeSnippetUserSnippet</key>
  <true/>
  <key>IDECodeSnippetVersion</key>
  <integer>2</integer>
</dict>
</plist>
```

Since snippets are so powerful and convenient, I decided to automate how my team
shares Xcode snippets at work. It was really easy, since Xcode snippets are just
text files. Setting up and sharing a standard set of snippets therefore basically
just involved setting up a shared folder with snippets files and writing a script
that copies these files to the correct place.

For my personal hobby projects, I just added a snippet folder to a setup project
that I have made public [here](https://github.com/danielsaidi/osx), then created
a script that copies these files to the correct place. The script looks like this:

```bash
#!/bin/bash

path_src=Snippets
path_dst=~/Library/Developer/Xcode/UserData/CodeSnippets
mkdir $path_dst
for file in $path_src/*.codesnippet; do
  cp $file $path_dst/$(basename $file)
done
```

At work, however, I added a snippet folder to the main app project root instead,
then added a bunch of general and company specific snippets to it. After that, I
extended the Fastlane `setup_xcode` lane to also copy all these code snippets to
their correct place, as such:

```
desc "Setup Xcode"
  lane :setup_xcode do |options|
    setup_xcode_tools
    setup_xcode_snippets
  end

  desc "Setup Xcode Tools"
  lane :setup_xcode_tools do |options|
    sh "cd .. && brew bundle"
  end

  desc "Setup Xcode snippets"
  lane :setup_xcode_snippets do |options|
    snippets_path = File.expand_path('../Snippets/*.codesnippet')
    snippets_paths = [snippets_path].flatten
    snippets = snippets_paths.map { |f| f.include?("*") ? Dir.glob(f) : f }.flatten
    target_path = File.expand_path('~/Library/Developer/Xcode/UserData/CodeSnippets')
    FileUtils.cp_r(snippets, target_path, remove_destination: true)
  end
```

Running `fastlane setup_xcode` will thus now run `brew bundle` AND copy snippets.
This means that we now have a way to setup Xcode, that we can easily extend with
more tasks whenever we need.


## Conclusion

Using shell scripts, dependency managers and Fastlane is a simple and convenient
way to setup Xcode with a single command. Tools like `swiftlint` makes it easy to
enforce common conventions, while Xcode snippets can be used to generate text and
comments that should follow a desired format.

Feel free to check out [my OS X setup script](https://github.com/danielsaidi/osx)
for some example scripts and snippets and let me know if you have any questions
or things to add to this discussion.



