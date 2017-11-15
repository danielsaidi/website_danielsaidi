---
title:  JSON in iOS
date: 	2012-07-04 21:25:00 +0100
tags: 	ios objective-c json
redirect_from:  /blog/mobile/2012/07/04/json-in-ios
---


I am currently creating an iOS app that will share data using JSON. Working with
JSON is trivial in iOS 5, since there is now a great, native JSON serializer and
deserializer. It works well, but I find it tedious to write all the required code
for creating and parsing JSON data over and over again.

To make my code cleaner, and get loose coupling to the native classes, I decided
to define a JSON serializer/deserializer protocol, then create an implementation
that uses the native iOS 5 JSON serializer under the hood.

I first created the (really) simple protocol:


```objc
#import <Foundation/Foundation.h>

@protocol ObjectSerializer <NSObject>

- (id)deserializeStringToObject:(NSString *)string;
- (NSString *)serializeObjectToString:(id)object;

@end
```


I then created a small implementation of this protocol, which looks like this:


```objc
#import <Foundation/Foundation.h>
#import "ObjectSerializer.h"

@interface NativeJsonSerializer : NSObject<ObjectSerializer>

@end
```


```objc
#import "NativeJsonSerializer.h"

@implementation NativeJsonSerializer

- (id)deserializeStringToObject:(NSString *)string
{
    NSData *data = [string dataUsingEncoding:NSUTF8StringEncoding];
    NSError *error = nil;
    id result = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingAllowFragments error:&error];
    if (!result) {
        NSLog(@"%@", error.description);
    }

    return result;
}

- (NSString *)serializeObjectToString:(id)data
{
    NSError *error;
    NSData *result = [NSJSONSerialization dataWithJSONObject:data options:NSJSONReadingAllowFragments|NSJSONWritingPrettyPrinted error:&error];
    if (!result) {
        NSLog(@"%@", error.description);
    }

    return [[NSString alloc] initWithData:result encoding:NSUTF8StringEncoding];
}

@end
```


Finally, I naturally have unit tests in place, that tests a lot of possible JSON
operations. It seems to work well.


```objc
#import <SenTestingKit/SenTestingKit.h>

@interface NativeJsonSerializerTests : SenTestCase

@end
```



```objc
#import "NativeJsonSerializerTests.h"
#import "NativeJsonSerializer.h"

@implementation NativeJsonSerializerTests

NativeJsonSerializer *_serializer;

- (void)setUp {
    [super setUp];
    _serializer = [[NativeJsonSerializer alloc] init];
}

- (void)tearDown {
    // Tear-down code here.
    [super tearDown];
}

- (void)test_serializerShouldDeserializeArray {
    NSArray *result = [_serializer deserializeStringToObject:@"[\"foo\",\"bar\"]"];

    bool error = FALSE;
    error = error && !(result.count == 2);
    error = error && !([(NSString *)[result objectAtIndex:0] isEqualToString:@"foo"]);
    error = error && !([(NSString *)[result objectAtIndex:1] isEqualToString:@"bar"]);

    if (error)
        STFail(@"NativeJsonSerializer could not deserialize array");
}

- (void)test_serializerShouldDeserializeDictionary {
    NSDictionary *result = [_serializer deserializeStringToObject:@"{\"foo\":\"bar\",\"bar\":\"foo\"}"];

    bool error = FALSE;
    error = error && !(result.count == 2);
    error = error && !([(NSString *)[result objectForKey:@"foo"] isEqualToString:@"bar"]);
    error = error && !([(NSString *)[result objectForKey:@"bar"] isEqualToString:@"foo"]);

    if (error)
        STFail(@"NativeJsonSerializer could not deserialize dictionary");
}

- (void)test_serializerShouldDeserializeFloat {
    NSNumber *result = [_serializer deserializeStringToObject:@"1.1"];
    if (![result floatValue] == 1.1)
        STFail(@"NativeJsonSerializer could not deserialize float");
}

- (void)test_serializerShouldDeserializeInteger {
    NSNumber *result = [_serializer deserializeStringToObject:@"1"];
    if (![result intValue] == 1)
        STFail(@"NativeJsonSerializer could not deserialize integer");
}

- (void)test_serializerShouldDeserializeString {
    NSString *result = [_serializer deserializeStringToObject:@"\"Foo Bar\""];
    if (![result isEqualToString:@"Foo Bar"])
        STFail(@"NativeJsonSerializer could not deserialize string");
}

- (void)test_serializerShouldSerializeArray {
    NSMutableArray *data = [[NSMutableArray alloc] initWithCapacity:2];
    [data addObject:@"foo"];
    [data addObject:@"bar"];

    NSString *result = [_serializer serializeObjectToString:data];
    NSString *expectedResult = @"[\n  \"foo\",\n  \"bar\"\n]";

    if (![result isEqualToString:expectedResult])
        STFail(@"NativeJsonSerializer could not serialize array");
}

- (void)test_serializerShouldSerializeDictionary {
    NSMutableDictionary *data = [[NSMutableDictionary alloc] init];
    [data setObject:@"bar" forKey:@"foo"];
    [data setObject:@"foo" forKey:@"bar"];

    NSString *result = [_serializer serializeObjectToString:data];
    NSString *expectedResult = @"{\n  \"foo\" : \"bar\",\n  \"bar\" : \"foo\"\n}";

    if (![result isEqualToString:expectedResult])
        STFail(@"NativeJsonSerializer could not serialize dictionary");
}

- (void)test_serializerShouldSerializeFloat {
    NSString *result = [_serializer serializeObjectToString:[NSNumber numberWithFloat:1.1]];
    if (![result isEqualToString:@"1.1"])
        STFail(@"NativeJsonSerializer could not serialize float");
}

- (void)test_serializerShouldSerializeInteger {
    NSString *result = [_serializer serializeObjectToString:[NSNumber numberWithInt:1]];
    if (![result isEqualToString:@"1"])
        STFail(@"NativeJsonSerializer could not serialize integer");
}

- (void)test_serializerShouldSerializeString {
    NSString *result = [_serializer serializeObjectToString:@"Foo Bar"];
    if (![result isEqualToString:@"\"Foo Bar\""])
        STFail(@"NativeJsonSerializer could not serialize string");
}

@end
```


