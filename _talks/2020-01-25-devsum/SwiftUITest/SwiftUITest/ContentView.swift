//
//  ContentView.swift
//  SwiftUITest
//
//  Created by Daniel Saidi on 2020-05-22.
//  Copyright © 2020 Daniel Saidi. All rights reserved.
//

import SwiftUI

struct ContentView: View {
    
    let food = Food.allCases
    
    var body: some View {
        NavigationView {
            List(food) { food in
                NavigationLink(destination: FoodView(food: food)) {
                    HStack {
                        VStack(alignment: .leading) {
                            Text(food.name).font(.headline)
                            Text(food.subtitle).font(.footnote)
                        }
                        Spacer()
                    }
                }
            }.navigationBarTitle("Food", displayMode: .inline)
        }
    }
}

struct FoodView: View {
    
    init(food: Food) {
        self.food = food
    }
    
    private let food: Food
    
    var body: some View {
        Text(food.name)
            .navigationBarTitle(food.name)
    }
}


private extension Food {
    
    var subtitle: String {
        canBePoured ? "Can be poured" : "Must be chewed"
    }
}

struct ContentView_Previews: PreviewProvider {
    static var previews: some View {
        ContentView()
    }
}


enum Food: String, Identifiable, CaseIterable {
    
    case pasta, hamburger, pizza, aspargus, meatballs, chicken, casserole, lasagna, salad, soup
    
    var id: String {
        rawValue
    }
    
    var name: String {
        rawValue.capitalized
    }
    
    var canBePoured: Bool {
        self == .soup
    }
}
