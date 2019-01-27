---
title:  Ditching RxSwift
date:   2018-01-19 20:20:00 +0100
tags:	swift rxswift
---


After hearing so many good things about `RxSwift` and not having the opportunity
to try it at work, I decided to use it when I rewrote an old app of mine.

However, after struggling with it for months, I still haven't found a nice setup.
I think my old code produces much better code (more readable, less complex, less
error-prone etc.). I therefore finally decided to ditch `RxSwift` completely.

In this blog post, I will motivate my decision and post before and after samples
to illustrate how I think ditching `RxSwift` makes my app better and more fun to
work with.


## About the app

The app is a pretty small map-based app, where users can save custom geo data. I
use `Realm` for persistency, which means that the data layer is synchronous. The
service design, however, is asynchronous in order to make the app flexible.

Since all services in tha app are (potentially) asynchronous, I thought this was
a great case to use `RxSwift`. I also decided to use rx for data binding and for
gestures as well, using `RxCocoa` and `RxGestures`. In this post, I'll call them
`RxSwift` to simplify things. They are, however, separate libraries.

As I begun adding `RxSwift` to my app, I quickly ran into some initial problems.


## Official Documentation

My first problems with `RxSwift` was the official documentation, which (I think)
goes too deep into the semantics and terminology from the get go, without a good
initial onboarding and understandable examples. I later found great blogs that I
think explain the concepts better, but think the official documentation could be
drastically improved.

For nice posts about `RxSwift` as well as reactive programming in general, check
out [Ray Wenderlich's](https://www.raywenderlich.com/138547/getting-started-with-rxswift-and-rxcocoa)
and [Adam Borek's](adamborek.com/) posts.


## Dispose bag

Being no stranger to reactive programming, I still think `RxSwift` requires some
strange setup. The `dispose bag` concept was such a thing, where you have to use
a dispose bag to manage how observables are disposed.

In this app, I had huge problems when using single a bag for service observables,
data bindings and gesture binding. Knowing when to manually dispose this bag was
a nightmare, since some observables should be disposed when a screen disappeared,
some when data was reloaded, some never etc. I never found a nice setup for this
and never found any good examples that fit my requirements.

Furthermore, Adam Borek has many great posts where he illustrates how easy it is
to introduce memory leaks by using a dispose bag incorrectly and how observables
will stop broadcasting if one update fails. Most of these examples are difficult
to catch at a glance, which means that it's very likely that you'll accidentally
add serious and hard-found bugs to your app if you use `RxSwift` without knowing
exactly what you're doing.


## Wrapping up my concerns

My overall impression after all this is that `RxSwift` makes it very easy to add
bugs and memory leaks to your apps, and requires you to handle built-in problems
by introducing complex setups. You have to know exactly how to use a dispose bag
(see Adam's post about why table view cells are tricky), otherwise your app will
become a mess. So in order to succeed with `RxSwift`, you will have to go all in.
So will the rest of your team.

This makes adding `RxSwift` to your app a big decision, since everyone is forced
to adopt a reactive programming style. If you compare it with e.g. `AwaitKit`, I
think the latter adds a great util set to your app without forcing you to change
your overall programming style. `RxSwift`, however, requires a whole other level
of commitment.

Furthermore, I think that `RxSwift` throws a too extensive toolbox at you, which
binds you harder and harder to the library. Besides the observables, singles and
completables, you'll soon face `drivers` and other ux-specific components.

Overall, I think you have to ask yourself if you think that `RxSwift` brings you
good value for all this complexity. I personally think the answer is `no`.


## Replacing `RxSwift` with vanilla `UIKit`

I will end this post with some examples on how some parts of the code looks with
`RxSwift` and how the same functionality looks with plain `UIKit`.


### Example 1: Alert

First, let's have a look at an alert class that I display when the user tapped a
"create new map" button. This is the (somewhat cleaned up) code with `RxSwift`:


```swift
class CreateMapAlert: UIAlertController {
    
    fileprivate lazy var service: MapService = IoC.resolve()
    
    static func present(from vc: UIViewController) -> Observable<Map?> {
        return Observable.create { observer in
            let alert = createActionSheet(with: observer)
            vc.present(alert, animated: true)
            return Disposables.create {
                alert.dismiss(animated: true, completion: nil)
            }
        }
    }
}

fileprivate extension CreateMapAlert {
    
    func addCancelAction(with observer: AnyObserver<Map?>) {
        let title = L10n.cancel.string
        let action = UIAlertAction(title: title, style: .cancel) { _ in
            observer.onCompleted()
        }
        addAction(action)
    }

    func addNameTextField() {
        addTextField { textField in
            textField.autocapitalizationType = .words
            textField.placeholder = L10n.name.string
        }
    }
    
    func addOkAction(with observer: AnyObserver<Map?>) {
        let title = L10n.create.string
        let action = UIAlertAction(title: title, style: .default) { _ in
            self.createMap(with: observer)
        }
        addAction(action)
    }
    
    static func createActionSheet(with observer: AnyObserver<Map?>) -> CreateMapAlert {
        let title = L10n.createMapTitle.string
        let message = L10n.createMapMessage.string
        let alert = CreateMapAlert(title: title, message: message, preferredStyle: .alert)
        alert.addNameTextField()
        alert.addOkAction(with: observer)
        alert.addCancelAction(with: observer)
        return alert
    }
    
    func createMap(with observer: AnyObserver<Map?>) {
        guard let text = textFields?.first?.text else { return }
        let name = text.trimmingCharacters(in: .whitespaces)
        guard name.count > 0 else { return }
        service.createMap(named: name).subscribe(onSuccess: { map in
            observer.onNext(map)
            observer.onCompleted()
        }, onError: { error in
            observer.onError(error)
        }).dispose()
    }
}
```

Presenting this alert involves catching the returned observable and adding it to
the dispose bag, like this:

```swift
func addMap() {
    let alert = CreateMapAlert.present(from: self)
    alert.subscribe(onNext: { [weak self] _ in
        self?.reloadMaps()
    }, onError: { [weak self] error in
        self?.alert(error)
    }).disposed(by: disposeBag)
}
```

When I first created this alert, I thought it was a pretty cool setup, but I now
find it to be unnecessarily complicated. 

Below is a plain `UIKit` replacement. Instead of an observable, I instead pass a
completion block. The present function thus doesn't return anything.

```swift
class CreateMapAlert: UIAlertController {
    
    fileprivate lazy var service: MapService = IoC.resolve()
    
    static func present(from vc: UIViewController, completion: @escaping (Map?) -> ()) {
        let title = L10n.createMapTitle.string
        let message = L10n.createMapMessage.string
        let alert = CreateMapAlert(title: title, message: message, preferredStyle: .alert)
        alert.addNameTextField()
        alert.addOkAction(with: completion)
        alert.addCancelAction(with: completion)
        vc.present(alert, animated: true)
    }
}


// MARK: Private functions

fileprivate extension CreateMapAlert {
    
    func addCancelAction(with completion: @escaping (Map?) -> ()) {
        let title = L10n.cancel.string
        addAction(UIAlertAction(title: title, style: .cancel) { _ in
            completion(nil)
        })
    }

    func addNameTextField() {
        addTextField { textField in
            textField.autocapitalizationType = .words
            textField.placeholder = L10n.name.string
        }
    }
    
    func addOkAction(with completion: @escaping (Map?) -> ()) {
        let title = L10n.create.string
        addAction(UIAlertAction(title: title, style: .default) { _ in
            let map = self.createMap()
            completion(map)
        })
    }
    
    func createMap() -> Map? {
        guard let text = textFields?.first?.text else { return nil }
        let name = text.trimmingCharacters(in: .whitespaces)
        guard name.count > 0 else { return nil }
        return service.createMap(named: name)
    }
}
```

Instead of adding a returned observable to a dispose bags, presenting this alert
only involves passing in a completion block, like this:

```swift
func addMap() {
    CreateMapAlert.present(from: self) { [weak self] in
        self?.reloadMaps()
    }
}
```

I think this is a much more readable and straightforward approach, which also is
a lot less error-prone. 


### Example 2: Tap Gesture

This example is a lot shorter, and involves tap gesture binding using `RxSwift`,
compared to the standard selector-based approach.

This is how you setup a bar button item a tap action with `RxSwift`:


```swift
func setupAddButton() {
    let add = UIBarButtonItem(barButtonSystemItem: .add, target: nil, action: nil)
    add.rx.tap.subscribe(onNext: { [weak self] in
        self?.addMap()
    }).disposed(by: disposeBag)
    navigationItem.rightBarButtonItem = add
}
```

As I ditched `RxSwift`, this is how I now set it up, using standard selectors:

```swift
func setupAddButton() {
    let action = #selector(addMap)
    let add = UIBarButtonItem(barButtonSystemItem: .add, target: self, action: action)
    navigationItem.rightBarButtonItem = add
}
```

Now, I don't like selectors at all, but I think the `UIKit` code is a lot better.
I would prefer action blocks, though.


### Example 3: Long Press Gesture

I usually prefer to setup outlets in the `didSet` block, instead of having setup
functions that I have to call in `viewDidLoad`. However, with `RxSwift`, setting
up a long press gesture is rather nasty, which made me set it up like this:

```swift
override func viewDidLoad() {
    super.viewDidLoad()
    setupMapView()
}

func setupMapView() {
    guard let view = mapView else { return }
    setupMapViewLongPress(for: view)
}

func setupMapViewLongPress(for view: MKMapView) {
    view.rx.longPressGesture()
        .when(.began)
        .subscribe(onNext: { [weak self] gesture in
            let point = gesture.location(in: view)
            let coordinate = view.convert(point, toCoordinateFrom: view)
            self?.addUnsavedAnnotation(at: coordinate)
        }).disposed(by: disposeBag)
}
```

If we'd add the rx code to the `didSet` block, I think the property would become
too big. With `UIKit`, however, I think this is not the case:

```swift
@IBOutlet weak var mapView: ComboMapView? {
    didSet {
        mapView?.showsUserLocation = true
        let action = #selector(mapViewLongPressed(_:))
        mapView?.addLongPressAction(action, target: self)
    }
}

func mapViewLongPressed(_ gesture: UILongPressGestureRecognizer) {
    guard let view = mapView else { return }
    let point = gesture.location(in: view)
    let coordinate = view.convert(point, toCoordinateFrom: view)
    addUnsavedAnnotation(at: coordinate)
}
```

By not using `RxSwift`, we also remove several indentation levels. Not too bad.


### Example 4: UITextView delegate

Now, this is a bit tricky. I hate having to implement `UITextViewDelegate` to be
able to handle what happens in a `UITextView`. However, consider this rx code:

```swift
override func viewDidLoad() {
    super.viewDidLoad()
    setupCommentTextView()
}

func setupCommentTextView() {
    guard let view = commentText else { return }
    setupCommentTextViewEditing(for: view)
}

func setupCommentTextViewEditing(for view: UITextView) {
    view.rx.didBeginEditing
        .subscribe(onNext: { [weak self] in
            self?.setupDoneButton(for: view)
            view.setupPlaceholderBeforeEditing()
        }).disposed(by: disposeBag)
    
    view.rx.didEndEditing
        .subscribe(onNext: { [weak self] in
            self?.endEditing(for: view)
            view.setupPlaceholderAfterEditing(text: L10n.enterComment.string)
        }).disposed(by: disposeBag)
}

```

I think the above is horrible, especially compared to the `UIKit` alternative:

```swift
@IBOutlet weak var commentTextView: UITextView? {
	didSet { commentTextView?.delegate = self }
}

func textViewDidBeginEditing(_ textView: UITextView) {
	setupDoneButton(for: textView)
	textView.setupPlaceholderBeforeEditing()
}

func textViewDidEndEditing(_ textView: UITextView) {
	endEditing(for: textView)
	textView.setupPlaceholderAfterEditing(text: L10n.enterComment.string)
}
```

So, delegates are horrible, but in my opinion, `RxSwift` is even worse.


## Conclusion

These are just my five cents. Maybe I've misunderstood everything about `RxSwift`.
I would love comments and discussions in the comment field below.
