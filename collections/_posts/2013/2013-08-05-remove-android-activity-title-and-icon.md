---
title: Remove Android Activity Title and Icon
date:  2013-08-05 09:39:00 +0100
tags:  android
---

![Counter](/assets/blog/2013-08-05-android.png)

My getting-to-know-and-to-love Android study session continues, and has now come 
to themes. This morning, I have been learning how to use themes to customize the
action bar and remove its icon and title.

At first, I removed the icon and title by running this piece of code in my start
activity's `onCreate` method:


```java
ActionBar actionBar = getActionBar();
actionBar.setDisplayShowHomeEnabled(false);
actionBar.setDisplayShowTitleEnabled(false);
```

This works, but causes the icon and title to be removed with a small delay. You
will see the icon and title while the activity loads, after which they fade away.

A better way to achieve this is to remove the icon and title with themes instead
of with code. I removed the code above and did the following instead:


## Create a default action bar style (optional)

You don't have to do this, but I think that having a default action bar style is
more consistent than just adding one for action bars without the icon and title.

In `styles.xml`, I added the following line to my app theme:


```xml
<style name="AppTheme" parent="AppBaseTheme">
    <item name="android:actionBarStyle">@style/ActionBar</item>
    ...any additional theme styles here
</style>
```

I then added the ActionBar style, which looks like this:

```xml
<style name="ActionBar" parent="android:Widget.Holo.Light.ActionBar.Solid.Inverse">
    <item name="android:background">...any color or image here...</item>
</style>
```

Note that I use the theme convention created by Android Studio, which creates an
AppBaseTheme as well as an AppTheme that inherits the base theme. This will make
it easy to customize your action bar.


## Create a second style without icon and title

With the base theme in place, let's create an app theme without a title and icon.
Below the AppTheme style tag, add this inheriting style:

```xml
<style name="AppThemeWithoutActionBarTitle" parent="AppTheme">
    <item name="android:actionBarStyle">@style/ActionBarWithoutTitle</item>
</style>
```

as well as a new action bar style:

```xml
<style name="ActionBarWithoutTitle" parent="@style/ActionBar">
    <item name="android:displayOptions">useLogo</item>
</style>
```


## Apply the new action bar style

To apply the new style, open your manifest file and add the following line to the
activities that should use it:

```xml
android:theme="@style/AppThemeWithoutActionBarTitle"
```

After this, the affected activities should not display the title or icon at all.