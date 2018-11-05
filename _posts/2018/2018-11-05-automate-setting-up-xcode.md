---
title:  Automate setting up Xcode
date:   2018-11-05 15:00:00 +0200
tags:	osx automation xcode fastlane
---

In this post, I will show how you can automate setting up Xcode with Fastlane to
keep your (and your team's) development environments consistent.


## Why automate?

I (and many developers with me) prefer to automate as many tasks as possible, to
reduce the amount of repetitive manual work, reduce the risk of human error and
increase overall reliability of the task in question. For developers, this often
includes unit testing, continous integration, release management etc.

For a team, automation is a handy way to ensure that it's easy for developers to
follow team and company conventions. One example is to use a `lint` tool to make
these conventions "compilable", so that not complying to them may cause warnings
and errors. Another example is to use code snippets to quickly generate code and
comments that should follow a certain format.


## Automate setting up `swiftlint` and other required Xcode tools

`swiftlint` is a great tool that can be injected into your app's build setup, to
trigger warnings and errors if your code doesn't follow a given set of rules and
guidelines. The default lint setup is pretty good, but you can customize it by
adding a `.swiftlint.yml` file to the project root, where you can ignore certain
rules, or tweak them to fit your conventions.

When you follow the SwiftLint readme, it suggests you to add a `Run Script Phase`
that looks like this:

```bash
if which swiftlint >/dev/null; then
    swiftlint
else
    echo "SwiftLint does not exist, download from https://github.com/realm/SwiftLint"
fi
```

However, since `swiftlint` is such a critical part of our process, I removed all
optionality and now just use a build phase that looks like this:

```bash
swiftlint
```

I then added a `Brewfile` to the project root, that contains these dependencies:

```
brew "carthage"
brew "swiftgen"
brew "swiftlint" 
```

A developer can now install all required external developer tools by typing the
following in the project root:

```
brew bundle
``` 

If she/he doesn't do this, or haven't installed these tools manually before, the
app will not build. So, simply put:

```
Do not make required, critical tools optional!
```

However, although `homebrew` is a standard developer tool, you could always make
this even more accessible to your developers by adding Xcode setup to `Fastlane`.
Just add two new lanes to your `Fastfile`, as such:

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

Now, your team members just have to type `fastlane setup_xcode` to install every
required tool. It may seem like a no-win, but the nice thing with this approach
is that it is extensible, as we'll see in the next section, as we automate Xcode
custom code snippet handling.



## Automate setting up custom Xcode code snippets

Code snippets is a nice Xcode tool, that makes it easy to auto generate code and
text that you often type. For instance, I use Xcode snippets to generate `MARK`
statements, extension regions, test suite imports, test suite body templates etc.
They save me a lot of time and make my code more uniform. It makes conforming to
common conventions effortless and automated.

I have therefore automated how we as a team share Xcode snippets at work, which
was really easy, since an Xcode snippet is just a text file with a certain name
and format. Setting up and sharing a standard set of snippets for a development
team therefore basically just involves setting up a shared folder with snippets
and writing a script that copies these files to the correct place.

For my personal hobby conventions, I have a standard setup script, which you can
download [here](https://github.com/danielsaidi/osx). I just added a code snippet
folder and a shell script that copies these snippet files to their correct place.

At work, however, I instead added a `Snippets` folder to the main app's project
root, then added a bunch of general and company specific snippets to the folder.
For instance, a snippet that creates a "plain" `// MARK - ` statement is defined
is defined in a file named `mark_plain.codesnippet`, that looks like this:

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

After that, I extended the Fastlane approach from above to also include copying
all code snippets to the correct place. The Fastlane setup then looked like this:

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
This means that we have a way to setup Xcode that we can extend to do more stuff
whenever we need it to, which means that developers can setup the required Xcode
environment with a single command.


## Conclusion

Using shell scripts, dependency managers and Fastlane to setup Xcode is a simple
and convenient way to let your team automatically keep your environments in sync.
Tools like `swiftlint` makes it easy to enfore common conventions, while snippets
make it easy to write non-compiled text and comments that follow a desired format.

Feel free to check out [my OS X setup script](https://github.com/danielsaidi/osx)
for some example scripts and snippets and let me know if you have any questions
or things to add to this discussion.



