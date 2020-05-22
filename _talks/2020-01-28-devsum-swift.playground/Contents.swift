import Foundation


/// Variables / constants **********************************

var variable: Bool = true
let constant: Bool = true


/// Types **************************************************

let myInt: Int = 3
let myDouble: Double = 3.1
// Remove the type definition
let myDecimal: Decimal = 3.14

// Show type inference with this function
// func print(decimal: Decimal) {}
// print(decimal: 1)

let myBool = true
let myString = "A plain string"
let myString2 = "A \(myBool) string"

let intArray: [Int] = [1, 2, 3]
//let array = [1, 2, 3]
let intDictionary: [String: Int] = ["Age": 41, "Length": 181]

let set = Set(intArray)

// OPTIONAL


/// Functions **********************************************

// Declare a function and call it, mention named parameters
// Omit the first function name, e.g. saveUser(user:)
// Trailing completion blocks

func doStuff() {}
func doStuff(with int: Int) {}

func parse(string: String) {}
func parseString(string: String) {}
func parseString(_ string: String) {}
func parse(_ string: String) {}

func login(userName: String, password: String, completion: (_ result: Bool) -> Void) {
    completion(true)
}
typealias LoginCompletion = (_ result: Bool) -> Void
// func login(userName: String, password: String, completion: LoginCompletion) {
//     completion(true)
// }


/// Enums **************************************************

enum Shape {
    
    case square, rectangle, circle, star(edges: Int)
    
    var hasEdges: Bool {
        switch self {
        case .star(let edges): return edges > 4
        default: return false
        }
    }
}

let myShape = Shape.square
func useShape(_ shape: Shape) {}
useShape(.circle)

// Switch / compare
// Add "hasCorners"
// Show functions
// Error



/// Optionality ********************************************

// Optionality is a choice

func print(age: Int) {
    print(age)
}

func printOptional(age: Int?) {
    guard let age = age else { return }
    print(age)
}

let myAge: Int? = 5

if let age = myAge {
    print(age: age)
} else {
    print("No age")
}

printOptional(age: 123)



/// Structs and classes ************************************

// struct User { var name: String }
// class User { var name: String }
// Make them implement equatable
// Classes can inherit, structs cannot

struct UserStruct: User {
    
    var name: String
}

class UserClass: User {
    
    init(name: String) {
        self.name = name
    }
    
    var name: String
}

// Play around with lets and vars

var user1 = UserStruct(name: "Daniel")
// user1.name = "Saidi"

var user2 = UserClass(name: "Daniel")
// user2.name = "Saidi"



/// Protocol ***********************************************

protocol User {

    var name: String { get set }
}

// Demonstrate AnyObject



/// Extensions *********************************************

extension User {
    
    mutating func anonymize() {
        name = ""
    }
}

user2.name = "Robert"
user2.name
user2.anonymize()
user2.name

// Make user AnyObject and show that mutating isn't needed



/// Generics ***********************************************

var array: [Int] = []
var array2 = [Int]()
var array3 = Array<Int>()

// array.append(2.3)
array.append(4)

class Store<ItemType> {
    
    private(set) var items = [ItemType]()
    
    func store(item: ItemType) {
        items.append(item)
    }
}

let myStore = Store<Int>()
// myStore.items = [1, 2, 3]
myStore.store(item: 4)
// Omit param -> myStore.store(4)
// myStore.store(user1)



/**
 ## SwiftUI
 */

import SwiftUI
import PlaygroundSupport

//
 let view = Group {
     VStack {
         Text("Get Swifty").font(.title)
         Text("with it").font(.body)
     }.background(Color.red)
 }

 let vc = UIHostingController(rootView: view)
 PlaygroundPage.current.liveView = vc
