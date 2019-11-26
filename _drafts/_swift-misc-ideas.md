Sheeeeet - static overrides like with height.
Overlay views from iExtra
Extend protocols, not types (SecondsDisplayable)


@Inverse(hasContent)
    var isEmpty: Bool { ... }
    
    @Inverse(isVisible)
    var isHidden: Bool { ... }
    
    @Invertable
    @Inverse(show)
    func hide() { }
    
    @Inverse(hide)
    func show() {}