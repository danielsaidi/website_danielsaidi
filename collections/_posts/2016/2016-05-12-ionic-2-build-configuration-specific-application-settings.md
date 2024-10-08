---
title: Ionic 2 - Build Configuration-Specific Settings
date:  2016-05-12 12:04:00 +0100
tags:  ios android hybrid-apps
---

In an Ionic 2 app that I'm building for iOS and Android, I want to use different application settings for different build configurations. Let's see how this can be achieved in Ionic 2.

One reason that I want to be able to use different settings for different configurations, is that I want to be able to use different api endpoints for development and production apps, disable tracking for dev apps, disable logging for production apps etc.

I want to be clear that I haven't read through the massive amount of information out there for Ionic 2, ES6, TypeScript, Angular 2 etc. If a better approach exists, please let me know.


## Step 1 - Create application settings classes

I want to have a default build configuration that is shared by all build configs, then be able to override any settings and add new ones when switching configuration.

As such, I have a base class that defines most settings:

```ts
// app/config/app-settings-base

export class AppSettingsBase {
  public apiUrl: string;
  public rssFeedUrl: string;

  constructor() {
     this.apiUrl = '';
     this.rssFeedUrl = 'http://rssdomain.com/rss.xml';
  }
}
```

This class defines an RSS feed url, but leaves the api url blank. This means that each build configuration *can* define a custom RSS feed value, but *must* define an api url.

Note that the class isn't injectable, which means that it can't be injected into components in the app. For that, we will use build configuration-specific settings classes.

Let's start off with the settings class that I will use for development:

```ts
// app/config/app-settings-debug

import {Injectable} from "angular2/core";
import {AppSettingsBase} from "../config/app-settings-base";

@Injectable()
export class AppSettings extends AppSettingsBase {
  constructor() {
    super();
    this.apiUrl = 'Debug API';
  }
}
```

This code contains an `AppSettings` class that inherits `AppSettingsBase` and sets a debug-specific value for the `apiUrl` property. This class *is* injectable, so we can use it in our app.

Let's add a second settings class, that will be used for release builds:

```ts
// app/config/app-settings-release

import {Injectable} from "angular2/core";
import {AppSettingsBase} from "../config/app-settings-base";

@Injectable()
export class AppSettings extends AppSettingsBase {
  constructor() {
    super();
    this.apiUrl = 'Release API';
  }
}
```

This file *also* contains an injectable `AppSettings` class that also inherits `AppSettingsBase` (you will only use one though), then sets a release-specific value for the `apiUrl` property.


## Step 2 -Use Gulp to apply the correct settings class

We can use Gulp to apply the correct app settings file when serving and building the app.

First, add `"gulp-rename" : "1.2.2"` (or later) to your `package.json` file. Then, require the file topmost in your `gulpfile.js`, like this:

```ts
rename = require('gulp-rename'),
```

After that, add the following build task to `gulpfile.js`:

```ts
gulp.task('copy-settings', function () {
  var settingsFileSuffix = isRelease ? 'release' : 'debug';
  var pathPrefix = 'app/config/app-settings-';
  return gulp
    .src(pathPrefix + settingsFileSuffix + '.ts')
    .pipe(rename('app-settings.ts'))
    .pipe(gulp.dest('app/config/'));
});
```

Finally, refer to this task in ``serve:before`` and ``build``:

```ts
gulp.task('serve:before', ['watch', 'copy-settings']);
```

```ts
gulp.task('build', ['clean', 'copy-settings'], function(done) {
```

If we run `ionic serve` or `ionic build`, gulp will generate a copy of `app-settings-debug.ts` in `app-settings.ts`. If you add `--release`, gulp will use `app-settings-release.ts` instead.

You can extend this functionality to support more build configurations. Just follow the same approach as above.


## Step 3 - Use the resulting settings class

We now generate a configuration-specific file every time we build or serve the app. Since it is auto-created, add `app/config/app-settings.ts` to `.gitignore` to avoid committing it.

We can now use the resulting settings class as normal. For instance, to verify that a correct settings file is applied, add the following to your `app.ts` file:

```ts
import {AppSettings} from './config/app-settings';

...

@App({
  ...
  providers: [PodcastService, AppSettings]
})
  export class MyApp {
    ...
    constructor(platform: Platform, settings: AppSettings) {
       console.log(settings.apiUrl);
       ...
  }
}

```

If things work are properly configurated, you should see different output when you build for development and for release. This means that the configuration works.