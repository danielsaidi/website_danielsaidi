---
title: Android Fullscreen Activity
date:  2013-08-02 09:14:00 +0100
tags:  android
image: /assets/blog/2013/2013-08-05-android.png
---

The best way for me to get my act together when learning new things, is to write
about it as early as possible. This way, I can return to my earlier posts and
verify that I knew nothing once, and that I hopefully have learned a few thing
along the way. Today, I thought that I should honor this strategy, by publishing
a simple base class that can be used for fullscreen Android activities.

![Image of an Android teacher]({{page.image}})

In this class, I have basically just stripped and refactored the boilerplate code
you get when creating a fullscreen activity, then exposed simple methods that you
can call from the subclass.

```java
import android.annotation.TargetApi;
import android.app.Activity;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.view.Window;

/**
If you inherit this class, call initFullscreenWithContentView
or initFullscreenWithContentViewId in onCreate, after setting
the content view.

Since going fullscreen will not resize the activity, consider
not using an action bar, since it will be partially hidden. A
setContentViewWithoutTitleBar method is available, and can be
used instead of setContentView.
*/

public class FullscreenActivity extends Activity {

    private static final boolean FULLSCREEN_AUTO = true;
    private static final int FULLSCREEN_AUTO_DELAY_MILLIS = 3000;
    private static final int FULLSCREEN_INIT_DELAY_MILLIS = 100;
    private static final boolean FULLSCREEN_TOGGLE_ON_CLICK = true;
    private static final int FULLSCREEN_HIDER_FLAGS = SystemUiHider.FLAG_HIDE_NAVIGATION;

    private Handler fullscreenHandler;
    private Runnable fullscreenRunnable;
    private SystemUiHider systemUiHider;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        fullscreenAfterDelay(FULLSCREEN_INIT_DELAY_MILLIS);
    }

    protected void fullscreenAfterDelay(int delayMillis) {
        if (systemUiHider == null) {
            return;
        }

        fullscreenHandler.removeCallbacks(fullscreenRunnable);
        fullscreenHandler.postDelayed(fullscreenRunnable, delayMillis);
    }

    protected void initFullscreenWithContentView(View view) {
        setupFullscreenHandler();
        setupSystemUiHandlerForView(view);
        setupContentViewClickBehavior(view);
        fullscreenAfterDelay(FULLSCREEN_INIT_DELAY_MILLIS);
    }

    protected void initFullscreenWithContentViewId(int id) {
        initFullscreenWithContentView(findViewById(id));
    }

    protected void setContentViewWithoutTitleBar(int resourceId) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(resourceId);
    }

    private void setupContentViewClickBehavior(View view) {
        view.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (FULLSCREEN_TOGGLE_ON_CLICK) {
                    systemUiHider.toggle();
                } else {
                    systemUiHider.show();
                }
            }
        });
    }

    private void setupFullscreenHandler() {
        if (fullscreenHandler == null) {
            fullscreenHandler = new Handler();
            fullscreenRunnable = new Runnable() {
                @Override
                public void run() {
                    systemUiHider.hide();
                }
            };
        }
    }

    private void setupSystemUiHandlerForView(View view) {
        systemUiHider = SystemUiHider.getInstance(this, view, FULLSCREEN_HIDER_FLAGS);
        systemUiHider.setup();
        systemUiHider.setOnVisibilityChangeListener(new SystemUiHider.OnVisibilityChangeListener() {
            @Override
            @TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
            public void onVisibilityChange(boolean visible) {
                if (visible && FULLSCREEN_AUTO) {
                    fullscreenAfterDelay(FULLSCREEN_AUTO_DELAY_MILLIS);
                }
            }
        });
    }
}
```

This is completely different from any iOS code that I've previously written. 
I used to write and teach Java at the university, but I'm really torn on it.
However, I have to say that it's way nicer than Objective-C.