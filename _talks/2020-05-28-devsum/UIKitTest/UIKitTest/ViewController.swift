//
//  ViewController.swift
//  UIKitTest
//
//  Created by Daniel Saidi on 2020-05-18.
//  Copyright © 2020 Daniel Saidi. All rights reserved.
//

import UIKit

class ViewController: UIViewController {

    override func viewDidLoad() {
        super.viewDidLoad()
        setupFoodList()
    }
    
    let food = Food.allCases
    
    func setupFoodList() {
        view.addSubview(foodTableView)
        foodTableView.translatesAutoresizingMaskIntoConstraints = false
        foodTableView.leadingAnchor.constraint(equalTo: view.leadingAnchor).isActive = true
        foodTableView.trailingAnchor.constraint(equalTo: view.trailingAnchor).isActive = true
        foodTableView.topAnchor.constraint(equalTo: view.topAnchor).isActive = true
        foodTableView.bottomAnchor.constraint(equalTo: view.bottomAnchor).isActive = true
        foodTableView.dataSource = self
        foodTableView.delegate = self
    }
    
    lazy var foodTableView = UITableView()
}

extension ViewController: UITableViewDataSource {

    func numberOfSections(in tableView: UITableView) -> Int { 1 }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        food.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = UITableViewCell(style: .default, reuseIdentifier: "Cell")
        // Just return the cell first to illustrate why nothing shows
        guard food.count > indexPath.row else { return cell }
        cell.textLabel?.text = food[indexPath.row].name
        return cell
    }
}

extension ViewController: UITableViewDelegate {
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let food = self.food[indexPath.row]
        performSegue(withIdentifier: "FoodDetails", sender: nil)
    }
}

enum Food: String, CaseIterable {
    
    case pasta, hamburger, pizza, aspargus, meatballs, chicken, casserole, lasagna, salad, soup
    
    var name: String {
        rawValue.capitalized
    }
    
    var canBePoured: Bool {
        self == .soup
    }
}
