---
title: Android Activity Lifecycle Car Analogy
date:  2013-08-05 10:25:00 +0100
tags:  android

image: /assets/blog/13/android.png
---

In most application frameworks, classes have a lifecycle that is used to do customizations at the proper time. Let's look at the Android activity lifecycle compared to a car.

![Image of an Android teacher]({{page.image}})

I saw [this great analogy](http://stackoverflow.com/questions/4553605/difference-between-onstart-and-onresume) for describing the Android Activity lifecycle
as a car. This can help us map where we should put your activity customizations.

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
    Intent i = new Intent(
        ACTION_GET_GROCERIES, 
        ...,  
        this, 
        GroceryStoreActivity.class
    );
}

protected void onRestart() {
    unlockCarAndGetIn();
    closeDoorAndPutOnSeatBelt();
    putKeyInIgnition();
}
```