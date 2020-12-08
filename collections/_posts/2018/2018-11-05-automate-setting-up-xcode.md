---
title: Automate setting up Xcode
date:  2018-11-05 15:00:00 +0200
tags:	 macos automation xcode fastlane lint
image: /assets/blog/xcode.png
---

In this blog post, I will show how to automate setting up Xcode for you and your
team, including setting up required tools, simplify enforcing common conventions
etc. using `Homebrew` and `Fastlane` in a way that is easy to extend if you need
to automate more tasks later on.


## Why automate?

I (and many with me) prefer to automate as many tasks as possible, to reduce the
amount of repetitive manual work, reduce the risk of human error and to increase
the overall reliability of a certain process. For good developers, this involves
unit testing, continuous integration, release management etc., for testers it can
involve automated UI testing etc. In short, if you can automate, then automate.

For a development team, automation can also be used to streamline the setup of a
certain developer environment and make it easy to follow shared conventions. For
instance, `swiftlint` can help to enforce code conventions, code snippets can be
used to generate comments and code blocks etc.


## Automate setting up required tools

Whenever you have a set of requirements in order to build and run a project, you
should consider using dependency and package managers to setup your dependencies,
so that your team can install all dependencies with a single command.

To achieve this, we can use tools like Homebrew and Fastlane and compose them in
a way that makes the setup process simple and painless.

For instance, you can add a `Brewfile` to your project root and add all required
tools to it:

```bash
brew "carthage"
brew "swiftgen"
brew "swiftlint" 
```

Your team can now install all these tools by typing this command in the terminal:

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


## swiftlint

`swiftlint` is a great tool that can be injected into the build process and will
trigger warnings and errors if your code doesn't follow certain conventions. The
standard setup is good, but you can customize it by adding a `.swiftlint.yml` to
the project root, in which you can ignore rules or tweak them to fit your style.

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


## Xcode snippets

Xcode snippets let you generate text that you type often. For instance, I use it
to generate `MARK` blocks, extension bodies, test suite imports, test suite body
templates etc. They save me a lot of time and makes my code look the same across
the entire code base, with no extra effort.

For instance, a snippet that creates a "plain" `// MARK - ` statement is defined
in a file named `mark_plain.codesnippet`, that looks like this:

```xml
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
shares these snippets. It was very easy, since snippets are just text files. The
setup therefore basically just involved setting up a shared folder with snippets
files and writing a script that copies these files to the correct place.

For my personal hobby projects, I just added a snippet folder to a setup project
that I have made public [here](https://github.com/danielsaidi/osx), then created
a script that copies these files to the correct place. It looks like this:

```bash
#!/bin/bash

path_src=Snippets
path_dst=~/Library/Developer/Xcode/UserData/CodeSnippets
mkdir $path_dst
for file in $path_src/*.codesnippet; do
  cp $file $path_dst/$(basename $file)
done
```

At work, however, I added a snippet folder to the main app project instead, then
added a bunch of general and company-specific snippets to it. After that, I just
extended the `setup_xcode` lane to copy all these code snippets to their correct
place, as such:

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

Running `fastlane setup_xcode` will now run `brew bundle` AND copy code snippets.
This means that we now have a way to setup Xcode, that we can easily extend with
more tasks whenever we need.


## Conclusion

Using shell scripts, dependency managers and Fastlane is a simple and convenient
way to setup Xcode with a single command. Tools like `swiftlint` make it easy to
enforce common conventions, while code snippets can be used to generate code and
comments that should follow a desired format.

Feel free to check out [my setup script](https://github.com/danielsaidi/osx) for
some examples, scripts and code snippets.