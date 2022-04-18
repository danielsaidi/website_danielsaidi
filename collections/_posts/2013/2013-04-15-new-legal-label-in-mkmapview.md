---
title: New legal label in MKMapView
date:  2013-04-15 21:29:00 +0100
tags:  ios objc geo
---

I'm using Google Maps in a couple of iOS apps. Or, at least I was before Apple 
replaced Google Maps with their own engine. So, now I guess I use Apple Maps.
And Apple Maps adds an annoying little label.

I have always admired Apple for their wonderful design, but when it comes to the
new `MKMapView` that is powered by Apple Maps, I find the new legal label annoying:

![Apple Maps In Action](/assets/blog/2013/2013-04-15-map.png)

Take a look at the lower-left corner. See the nice little round button? Shouldn't
it be even prettier pushed down even more into the corner? Yeah, I know...I think
so too. However, that area is occupied by a legal label that Apple for some reason
have decided to include.

I haven't found a way to remove this label with the native `MKMapView` methods, but
managed to find a way to programmatically move it to the bottom-right corner instead, 
using a custom category:


```objc
#import <MapKit/MapKit.h>

@interface MKMapView (LegalLabel)

typedef enum {
    MKMapViewLegalLabelPositionBottomLeft = 0,
    MKMapViewLegalLabelPositionBottomCenter = 1,
    MKMapViewLegalLabelPositionBottomRight = 2,
} MKMapViewLegalLabelPosition;

@property (nonatomic, readonly) UILabel *legalLabel;

- (void)moveLegalLabelToPosition:(MKMapViewLegalLabelPosition)position;

@end
```

```objc
#import "MKMapView+LegalLabel.h"

@implementation MKMapView (LegalLabel)

#pragma mark - Properties

- (UILabel *)legalLabel
{
    return [self.subviews objectAtIndex:1];
}

#pragma mark - Public methods

- (void)moveLegalLabelToPosition:(MKMapViewLegalLabelPosition)position
{
    UILabel *label = self.legalLabel;
    CGPoint point = [self getPointForLabel:label atPosition:position];
    label.center = point;
}

#pragma mark - Private methods

- (CGPoint)getPointForLabel:(UILabel *)label atPosition:(MKMapViewLegalLabelPosition)position
{
    int x = 0;

    switch (position) {
        case MKMapViewLegalLabelPositionBottomLeft:
            x = label.center.x;
            break;
        case MKMapViewLegalLabelPositionBottomCenter:
            x = self.center.x;
            break;
        case MKMapViewLegalLabelPositionBottomRight:
            x = self.frame.size.width - label.center.x;
            break;
    }

    CGPoint result = CGPointMake(x, label.center.y);
    return result;
}

@end
```

I have used this in an app that got approved, so I guess you can all just use it
without being concerned that you do something you're not allowed to do.