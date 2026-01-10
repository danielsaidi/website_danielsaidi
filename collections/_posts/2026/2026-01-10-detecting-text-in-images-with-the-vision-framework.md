---
title:  Detecting text in images with the Vision framework
date:   2026-01-10 07:00:00 +0100
tags:   swift swiftui vision

image-show: 0
image: /assets/blog/26/0110/image.jpg
assets: /assets/blog/26/0110/

docs: https://developer.apple.com/documentation/vision
video: https://developer.apple.com/videos/play/wwdc2025/272
wally: https://wally.app
---

[Apple's Vision framework]({{page.docs}}) is an amazing framework for performing machine learning-based image analysis directly on your device. Let's see how we can use it to extract text from images.


## Vision APIs

The Vision framework is available on iOS, macOS, iPadOS, tvOS, and visionOS, and lets you perform image analysis directly on your device. Before WWDC25, it had 31 public APIs:

![Vision Framework APIs]({{page.assets}}/vision-apis.jpg)

At WWDC25, [Apple announced 2 new Vision APIs]({{page.video}}) for reading documents and smudge detection. Let us see how we can use two of these APIs to detect text in images.


## Detecting text in images

I currently use a `RecognizeTextRequest` in my [Wally app]({{page.wally}}), to make it easy for users to parse text from their string, to add it as contextual information to an item:

![Wally app screenshot with parsed text]({{page.assets}}/wally.jpg){:width="250"}

By tapping the scan button, the app parses both images and presents the detected texts in a picker. As you can see, multi-line texts are parsed as individual lines.


## Detecting text with an (old) VNRecognizeTextRequest

I first implemented this feature using an app-specific `VNRecognizeTextRequest` that returns detected text by calling a completion handler:

```swift
extension VNRecognizeTextRequest {

    static func wallyRequest(
        completion: @escaping (([String]) -> Void)
    ) -> VNRecognizeTextRequest {
        let request = VNRecognizeTextRequest { request, error in
            if let error = error { return print(error) }
            guard let observations = request.results as? [VNRecognizedTextObservation] else { return }
            let texts = observations.compactMap { $0.topCandidates(1).first?.string }
            completion(texts)
        }
        request.recognitionLevel = .accurate
        request.usesLanguageCorrection = true
        return request
    }
}
```

To use this request in SwiftUI, I used an `ObservableObject` that wrote text to a published property:

```swift
class TextRecognitionContext: ObservableObject {
    
    init() {}
    
    @Published
    var recognizedText: [String] = []
    
    func reset() { recognizedText = [] }
}

extension TextRecognitionContext {
    
    func recognizeText(in images: [ImageRepresentable]) {
        reset()
        let images = images.compactMap { $0.cgImage }
        for image in images {
            let requestHandler = VNImageRequestHandler(cgImage: image, options: [:])
            let request = VNRecognizeTextRequest.wallyRequest { [weak self] texts in
                guard let self else { return }
                DispatchQueue.main.async {
                    self.recognizedText.append(contentsOf: texts)
                }
            }
            do {
                try requestHandler.perform([request])
            } catch {
                // Handle error
            }
        }
    }
}
#endif
```

In the code above, I convert each image to a `CGImage`, then iterate over the images and perform the request with a `VNImageRequestHandler`. Each detection adds the detected lines of text to a published `recognizedText` property, which automatically updates the picker.

This works, but the code is quite old and uses `ObservableObject` instead of `@Observable`. Let's use the newer `RecognizeTextRequest` to simplify things and support concurrency.


## Detecting text with a RecognizeTextRequest

With `RecognizeTextRequest` things become a lot easier. First of all, our app-specific request becomes easier to create, since we don't need to pass in a completion:

```swift
extension RecognizeTextRequest {

    static var wallyRequest: Self {
        var request = RecognizeTextRequest()
        request.recognitionLevel = .accurate
        request.usesLanguageCorrection = true
        return request
    }
}
```

We can then implement an async function that performs the text recognition and returns the result:

```swift
public extension RecognizeTextRequest {

    func recognizeText(
        in images: [ImageRepresentable]
    ) async throws -> [String] {
        let images = images.compactMap { $0.cgImage }
        var results: [String] = []
        for image in images {
            let observations = try await perform(on: image)
            let texts = observations.compactMap {
                $0.topCandidates(1).first?.string
            }
            results.append(contentsOf: texts)
        }
        return results
    }
}
```

We can then define our context as `@Observable` instead of `ObservableObject`, to use modern SwiftUI:

```swift
@Observable public class TextRecognitionContext {

    public init() {}

    public var recognizedText: [String] = []

    public func reset() {
        recognizedText = []
    }
}
```

We can then finally create a new context function that uses the new `RecognizeTextRequest` to detect text and update the `recognizedText`:

```swift
public extension TextRecognitionContext {

    func recognizeText(
        in images: [ImageRepresentable]
    ) {
        reset()
        Task {
            do {
                let request = RecognizeTextRequest.wallyRequest
                let texts = try await request.recognizeText(in: images)
                await MainActor.run {
                    self.recognizedText = texts
                }
            } catch {
                // ...
            }
        }
    }
}
```

This approach involves a little more code than before, but it's a lot easier to read and reason about.



## Text Recognizer Drawbacks

While the approach above works, a text recognizer returns unstructured text. Text that spans over multiple lines is separated in different lines, and it's up to us to make sense of it all.

Since the `RecognizeTextRequest` observations contain bounding boxes and contextual information, it's possible for us to group related texts together. 

However, with the new `RecognizeDocumentsRequest` that was introduced in WWDC 25, such manual work is no longer needed. Let's see how we can use it to simplify things.



## Detecting text with a RecognizeDocumentsRequest

With `RecognizeDocumentsRequest` things become even easier. First, our app-specific request becomes even easier to set up, using a single configuration:

```swift
extension RecognizeDocumentsRequest {

    static var wallyRequest: Self {
        var request = RecognizeDocumentsRequest()
        request.textRecognitionOptions.useLanguageCorrection = true
        return request
    }
}
```

The `recognizeText(in:)` function also becomes a lot cleaner, where we parse a document per image and extract complete text paragraphs instead of individual lines:

```swift
public enum RecognizeDocumentsRequestError: Error {
    case noDocumentFound
}

public extension RecognizeDocumentsRequest {

    func recognizeText(
        in images: [ImageRepresentable]
    ) async throws -> [String] {
        let images = images.compactMap { $0.cgImage }
        var results: [String] = []
        for image in images {
            let observations = try await perform(on: image)
            guard let doc = observations.first?.document else {
                throw RecognizeDocumentsRequestError.noDocumentFound
            }
            let texts = doc.paragraphs.map { $0.transcript }
            results.append(contentsOf: texts)
        }
        return results
    }
}
```

The `TextRecognitionContext` is also easily adjusted, by replacing the old request with the new one:

```swift
public extension TextRecognitionContext {

    func recognizeText(
        in images: [ImageRepresentable]
    ) {
        reset()
        Task {
            do {
                let request = RecognizeDocumentsRequest.wallyRequest
                let texts = try await request.recognizeText(in: images)
                await MainActor.run {
                    self.recognizedText = texts
                }
            } catch {
                // ...
            }
        }
    }
}
```

With this simple code change, the [Wally app]({{page.wally}}) feature becomes a lot better, since multi-line texts are now regarded as a single element:

![Wally app screenshot with parsed text]({{page.assets}}/wally-new.jpg){:width="250"}

The way we combine Vision requests with SwiftUI makes it easy to use text recognition in our apps. The same approach can be applied to the many other Vision APIs.


## Conclusion

The old Vision syntax never sat well with me, where you had to create a request as well as a request handler. 
The new `RecognizeTextRequest` and `RecognizeDocumentsRequest` types make things easier and work well with SwiftUI observations and Swift concurrency.

Make sure to check out the great [video]({{page.video}}) where Apple presents the new documents request. It can do so much more, like parsing tables, scan codes, and analyzing data:

![Vision parsed data]({{page.assets}}/vision-data.png){:class="plain"}

I'm very impressed by these tools that Apple provides to us, and will try to get myself to use them more in my various apps and SDKs.