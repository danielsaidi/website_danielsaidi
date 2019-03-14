These are the things I setup in new projects


1. Create a new project

Setup git. If you don't use it, you really should.
This is the .gitignore that I use
If you don't have GitHub/GitLab, setup a file system remote that syncs, e.g. Dropbox.



2. Create a domain project with a unit test

Ideally, your app should only contain app logic.
By adding a domain project, you have a natural place to put your models, services etc.
You can remove the domain scheme
Edit the app scheme and ensure that the domain and unit test project builds and tests
Run unit tests cmd+U to ensure that everything works correctly



3. Create empty project structure

I usually add some folders in both app and domain that describe my app, e.g.
App->Common - Place base classes, main view controllers, app-specific extensions etc.
App+Domain->Model -> Models, Services, Extensions
Discuss why sub-domains are better than "ViewControllers", "Views" etc.



4. SwiftLint

Setup SwiftLint globally
Add a run script step after target dependencies, so you don't have to wait for the app to compile before lint:

A really important thing, bcs: ... Actually, a more learning experience is to write your entire app, then
smack on SwiftLint at the very end...and see how your illusions of yourself being a flawless developer shatters.

These are the rules I sidable, and why I disable them: ...



5. SwiftGen

SwiftGen is great bcs: ... I normally use it for localized strings, image assets and fonts. 

You can install it with CocoaPods, which is nice, since it forces everyone to use the same version. However, I use to
run with the latest in my own projects. You can install it with homebrew.



6. Localizations

Sure, your app may be unilingual today, but there will come a day when you may want to localize, and to then
have language "keys" shattered all over your app is NOT a nice experience. Localize from the get-go.


To work securely with localizations, make sure to only work with generated enums. I use SwiftGen.

	Create a Localizable.strings file
	Select the file and tap "Localize" in the right panel
	This moves the file to...
	I move it into a folderless "Supporting Files" group
	create a Localizations folder
	create a Localizable.swift file in it
	add this run script

swiftgen strings -t structured-swift4 -o ${PROJECT_DIR}/PROJECT_NAME/Localization/Localizable.swift ${PROJECT_DIR}/PROJECT_NAME/en.lproj/Localizable.strings

+ How to use it


7. Image Assets

To work securely with image assets, instead of Image(named:), I use SwiftGen as well:

	Create a Resources folder
	Move the asset catalog there
	Create a Assets.swift file in it
	Add this run script

swiftgen xcassets -t swift4 -o ${PROJECT_DIR}/PROJECT_NAME/Resources/Assets.swift ${PROJECT_DIR}/PROJECT_NAME/Resources/Assets.xcassets

+ How to use it



8. Fonts

To work securely with fonts, yada yada

	Create a Fonts folder in Resources
	Create a Fonts.swift file in it
	Add your font files there
	Add this run script

swiftgen fonts -t swift4 -o ${PROJECT_DIR}/PROJECT_NAME/Resources/Fonts.swift ${PROJECT_DIR}/PROJECT_NAME/Resources/Fonts

+ How to use it



9. Fastlane

I always use Fastlane, but depending on if it is an app or a lib, I use it slightly different.

Both gets this unit test runner: xxx

For libraries, I have this version runner: xxx

At work, I use Match for certs and profiles and use Fastlane to release betas and testflights and prod apps.



10. Carthage / CocoaPods

While I initially loved CocoaPods (dev pods is still great), I have come to love Carthage even more.

	How to install?
	How to add carthage libs to app project
		General -> Add Linked Frameworks and Libraries FIRST (creates folder)
		General -> Embedded Binaries
		Build Phases -> Add [Carthage] Copy Frameworks
	How to add carthage libs to test/domain project
		General -> Link Binary with Librares
		Copy Files -> Add frameworks

I only use CocoaPods if there is no Carthage...and probably not if it hasn't.



11. IoC

Inversion of Control is SUPER important - if you don't already, do it now.
I don't use storyboards because of this, since I want to use constructor injections instead of lazy props.

	Dip vs Swinject
	IoC approach
	Link to iExtra

If you can't use constructor injections, use lazy dependency properties instead.



12. Unit testing

Quick/Nimble
Describe, vs. context
Examples



13. Architecture

Services, protocols, models etc.



12. App-navigation

Etension vs. service



13. Shared Snippets

Files, repo + script



14. Theme Handling

Apply/reapply (dark mode)


xx. Notifications

How to type them



15. App Icon / App Store

I use Sketch to creat emy app icons, and have setup an app folder strurcture and Sketch template that I reuse. 

You can download them here.

- App icon
- AppStore template
- Sticker Pack template