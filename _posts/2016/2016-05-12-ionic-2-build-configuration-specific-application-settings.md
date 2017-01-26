---
title:  "Ionic 2 - Build Configuration-Specific Application Settings"
date:   2016-05-12 12:04:00 +0100
categories: apps
tags: 	ionic cordova ios android gulp angular typescript
---


In an Ionic 2 app that I am building for iOS and Android, I want to use different
application settings for different build configurations. For instance, I want to
use different api endpoints for development and production apps, disable tracking
for development apps, disable logging for production apps etc.

Before we start, I want to be clear that I haven't read through the massive amount
of information out there for Ionic 1, Ionic 2, ES6, TypeScript, Angular 1, Angular
2 etc. If a better approach exist, please let me know.



## Step 1 - Create application settings classes

I want the app to have a default configuration that is shared by all build configs,
then be able to override any settings and add new ones when switching configuration.


As such, I have a base class that defines most settings:

**app/config/app-settings-base:**

{% highlight typescript %}
export class AppSettingsBase {
  public apiUrl: string;
  public rssFeedUrl: string;

  constructor() {
     this.apiUrl = '';
     this.rssFeedUrl = 'http://rssdomain.com/rss.xml';
  }
}
{% endhighlight %}

This base class defines an rss feed url, but leaves the api url blank. This means
that all available build configurations can define a custom rss feed value, but
does not have to. However, each configuration **must** define a specific api url.

Note that the class is not injectable. This means that it can not be injected into
different components in the app. For that, we will use build configuration-specific
settings classes.


Let's start off with the settings class that I will use for development:

**app/config/app-settings-debug:**

{% highlight typescript %}
import {Injectable} from "angular2/core";
import {AppSettingsBase} from "../config/app-settings-base";

@Injectable()
export class AppSettings extends AppSettingsBase {
  constructor() {
    super();
    this.apiUrl = 'Debug API';
  }
}
{% endhighlight %}

This file contains an *AppSettings* class that inherits *AppSettingsBase* and sets
a debug-specific value for the apiUrl property.

This class *is* injectable, which means that we can use it in our app.


Let's add a second settings class, that will be used for release builds:

**app/config/app-settings-release:**

{% highlight typescript %}
import {Injectable} from "angular2/core";
import {AppSettingsBase} from "../config/app-settings-base";

@Injectable()
export class AppSettings extends AppSettingsBase {
  constructor() {
    super();
    this.apiUrl = 'Release API';
  }
}
{% endhighlight %}

This file *also* contains an injectable AppSettings class that also inherits
*AppSettingsBase* (you will only use one though), then sets a release-specific
value for the apiUrl property.



## Step 2 -Use Gulp to apply the correct settings class

We will now use Gulp to apply the correct application settings file when serving
and building the app.

First, add **"gulp-rename" : "1.2.2"** (or later) to your **package.json** file.
Then, require the file topmost in your **gulpfile.js**, like this:

{% highlight typescript %}
rename = require('gulp-rename'),
{% endhighlight %}

After that, add the following build task to **gulpfile.js**:

{% highlight typescript %}
gulp.task('copy-settings', function () {
  var settingsFileSuffix = isRelease ? 'release' : 'debug';
  var pathPrefix = 'app/config/app-settings-';
  return gulp
    .src(pathPrefix + settingsFileSuffix + '.ts')
    .pipe(rename('app-settings.ts'))
    .pipe(gulp.dest('app/config/'));
});
{% endhighlight %}

Finally, refer to this task in **serve:before** and **build**:

{% highlight typescript %}
gulp.task('serve:before', ['watch', 'copy-settings']);
{% endhighlight %}

{% highlight typescript %}
gulp.task('build', ['clean', 'copy-settings'], function(done) {
{% endhighlight %}

If we now run `ionic serve` or `ionic build`, gulp will automatically generate a
copy of *app/config/app-settings-debug.ts* in **app/config/app-settings.ts**. If
you add `--release` to the command, gulp will use **app/config/app-settings.ts**
instead.

You could easily extend this functionality to support more build configurations.
Just follow the same approach as above.



## Step 3 - Use the resulting settings class

We have now generate a configuration-specific file generated every time we build
or serve the app. Since the file is auto-created, you should go ahead and add
*app/config/app-settings.ts* to *.gitignore*, if you have one. We do not need to
commit it every time it is changed.

You can now use the resulting settings class as normal. For instance, to verify
that the correct settings file is applied, add the following to your *app.ts* file:

{% highlight typescript %}
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

{% endhighlight %}

If everything works as expected, you will see different output when you build for
development and for release.

