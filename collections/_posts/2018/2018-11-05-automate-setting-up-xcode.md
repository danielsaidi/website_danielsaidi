---
title: Automate setting up Xcode
date:  2018-11-05 15:00:00 +0200
tags:	 article xcode macos automation fastlane lint
icon:  swift

image: /assets/blog/xcode.png
---

In this post, I will show how to automate setting up Xcode using `Homebrew` and
`Fastlane` in a way that is easy to extend if you need to automate more later on.

![Xcode]({{page.image}}){:class="plain" width="150px"}


## Why automate?

I (and many with me) prefer to automate as many tasks as possible, to reduce the
amount of repetitive manual work, reduce the risk of human error and to increase
the overall reliability of a certain process.

For a development team, automation can also be used to streamline the setup of a
certain developer environment and make it easy to follow shared conventions. For
instance, `swiftlint` can help to enforce code conventions, code snippets can be
used to generate comments and code blocks etc.


## External dependencies

When you have external dependencies for building and runing a project, you should
consider using dependency and package managers to setup your dependencies with a
single command.

To achieve this, we can use tools like Homebrew and Fastlane and compose them in
a way that makes the setup process simple and painless.

For instance, you can add a `Brewfile` to your project root and add all required
tools to it:

```bash
brew "carthage"
brew "swiftgen"
brew "swiftlint" 
```

Your can now install all these tools by typing a single command in the Terminal:

```bash
brew bundle
``` 

Although `homebrew` is a standard tool, you can always make this setup even more
accessible by adding it to `Fastlane`. Just add this lane to your `Fastfile`:

```
desc "Setup Xcode"
lane :setup_xcode do |options|
  sh "cd .. && brew bundle"
end
```

Now, you just have to type `fastlane setup_xcode` to install all tools. It may
seem like a no-win for now, but the nice thing with this approach is that it
can easily be extended to handle even more tasks.


## Linting

Linting is a way to automatically check your code for programmatic and stylistic
errors. To do this, you use a code analyzer tool called a linter.

`swiftlint` is a great linter for Swift. It triggers warnings and errors if the 
code doesn't follow certain conventions. The standard setup is good, but you can
customize it by adding a `.swiftlint.yml` to the project root, in which you can
ignore rules, tweak them to fit your style and add new ones.

When you read the swiftlint readme, it suggests you to add a `Run Script Phase`
to your target, that looks like this:

```bash
if which swiftlint >/dev/null; then
    swiftlint
else
    echo "SwiftLint does not exist, download from https://github.com/realm/SwiftLint"
fi
```

However, since swiftlint is such a critical tool, I prefer to have it mandatory.
I therefore skip the if/else check and just have:

```bash
swiftlint
```

This means that the app now fails to build whenever swiftlint isn't available,
which means that it has gone from being an optional to a required tool.

If you build packages with Swift Package Manager, you will not have a build phase
where you can inject swiftlint. I then inject swiftlint into certain Fastlane lanes,
so that linting is done in critical processes, like making new versions of my
open-source projects.


## Xcode snippets

Xcode snippets let you generate text that you type often. For instance, I use it
to generate `MARK` blocks, extension bodies, test suite imports, test templates
etc. They save me a lot of time and makes my code look the same across the entire
code base, with no extra effort.

For instance, I have a snippet that creates a "plain" `// MARK - ` statement, and
have it defined in a file named `mark_plain.codesnippet`:

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

Since snippets are so powerful, I decided to automate how my team shares these
snippets. It was very easy, since snippets are just text files. It therefore
basically just involved setting up a shared folder with snippets files and
writing a script that copies these files to the correct place.

For my personal projects, I just added a snippet folder to a setup project that
I have made public [here](https://github.com/danielsaidi/osx), then created
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

At work, where the team works on multiple projects that share conventions, I
added a snippet folder to a private repo, then added general and work-specific
snippets to it. After that, I extended `setup_xcode` to copy all these code
snippets to their correct place, as such:

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
way to setup Xcode with a single command, which can help you standardize things
in your personal or work environment. Tools like `swiftlint` make it easy to enforce 
conventions, while code snippets can generate code and comments that should follow
a desired format.

Feel free to check out [my setup script](https://github.com/danielsaidi/osx) for
some examples, scripts and code snippets.