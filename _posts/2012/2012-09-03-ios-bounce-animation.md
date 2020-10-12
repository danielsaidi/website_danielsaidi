---
title: iOS Bounce Animation
date:  2012-09-03 17:44:00 +0100
tags:  ios objc
---

I have an app with a main menu, where users can swipe horizontally through a set
of large icons that each take the user to a certain part of the app. When an icon
is tapped, it bounces, plays a sound, then takes the user to that particular part
of the app.

After trying out many different animations, I fell in love with a nice and clean
bounce animation that does its job without being too obvious. Unfortunately, the
link to the original implementation is now dead.

I modified the code by creating a UIView category for the animation, and added a
bounce factor so the effect can be used in various situations. I should probably
do the same for the repeat count as well, but that's a later issue.

Here's the code for the category:

**UIView+Bounce.h:**

```objc
#import <UIKit/UIKit.h>

@interface UIView (Bounce)

- (void)bounce:(float)bounceFactor;

@end
```

**UIView+Bounce.m:**

```objc
#import "UIView+Bounce.h"
#import <QuartzCore/QuartzCore.h>

@implementation UIView (Bounce)

+ (CAKeyframeAnimation*)dockBounceAnimationWithViewHeight:(CGFloat)viewHeight
{
    NSUInteger const kNumFactors    = 22;
    CGFloat const kFactorsPerSec    = 30.0f;
    CGFloat const kFactorsMaxValue  = 128.0f;
    CGFloat factors[kNumFactors]    = {0,  60, 83, 100, 114, 124, 128, 128, 124, 114, 100, 83, 60, 32, 0, 0, 18, 28, 32, 28, 18, 0};

    NSMutableArray* transforms = [NSMutableArray array];

    for(NSUInteger i = 0; i < kNumFactors; i++)
    {
        CGFloat positionOffset  = factors[i] / kFactorsMaxValue * viewHeight;
        CATransform3D transform = CATransform3DMakeTranslation(0.0f, -positionOffset, 0.0f);

        [transforms addObject:[NSValue valueWithCATransform3D:transform]];
    }

    CAKeyframeAnimation* animation = [CAKeyframeAnimation animationWithKeyPath:@"transform"];
    animation.repeatCount           = 1;
    animation.duration              = kNumFactors * 1.0f/kFactorsPerSec;
    animation.fillMode              = kCAFillModeForwards;
    animation.values                = transforms;
    animation.removedOnCompletion   = YES; // final stage is equal to starting stage
    animation.autoreverses          = NO;

    return animation;
}

- (void)bounce:(float)bounceFactor
{
    CGFloat midHeight = self.frame.size.height * bounceFactor;
    CAKeyframeAnimation* animation = [[self class] dockBounceAnimationWithViewHeight:midHeight];
    [self.layer addAnimation:animation forKey:@"bouncing"];
}

@end
```