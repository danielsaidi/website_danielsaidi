---
title: Android Activity Lifecycle Car Analogy
date:  2013-08-05 10:25:00 +0100
tags:  android
image: /assets/blog/2013/2013-08-05-android.png
---

In most application frameworks, classes have a certain lifecycle that may be used
to do customizations at the proper time. Let's look at the Android activity lifecycle
as compared to being a car.

![Image of an Android teacher]({{page.image}})

Today, I found [this great analogy](http://stackoverflow.com/questions/4553605/difference-between-onstart-and-onresume) for describing the Android Activity lifecycle
as a car, which can help you map where you should put your activity customizations.

```java
protected void onCreate(...) {
    openGarageDoor();
    unlockCarAndGetIn();
    closeCarDoorAndPutOnSeatBelt();
    putKeyInIgnition();
}

protected void onStart() {
    startEngine();
    changeRadioStation();
    switchOnLightsIfNeeded();
    switchOnWipersIfNeeded();
}

protected void onResume() {
    applyFootbrake();
    releaseHandbrake();
    putCarInGear();
    drive();
}

protected void onPause() {
    putCarInNeutral();
    applyHandbrake();
}

protected void onStop() {
    switchEveryThingOff();
    turnOffEngine();
    removeSeatBeltAndGetOutOfCar();
    lockCar();
}

protected void onDestroy() {
    enterOfficeBuilding();
}

protected void onReachedGroceryStore(...) {
    Intent i = new Intent(ACTION_GET_GROCERIES, ...,  this, GroceryStoreActivity.class);
}

protected void onRestart() {
    unlockCarAndGetIn();
    closeDoorAndPutOnSeatBelt();
    putKeyInIgnition();
}
```