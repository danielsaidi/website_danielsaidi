---
title: Custom ASP.NET validation attributes
date:  2011-03-23 12:00:00 +0100
tags:  archive
icon:  dotnet
---

ASP.NET validation attributes is a great way to validate C# properties in different ways, both client & server side. Let's look at how we can create our own validation attributes.

I use custom validation attributes all the time, for instance to validate:

- E-mail addresses
- Postal codes
- Social security numbers
- URLs

These could to some extent be validated with regular expressions, but some may require a deeper level of validation than just looking at string format.

Custom validation attributes can perform basic regex validation client-side, then perform a more thorough validation server-side if needed. 

Let's look at some examples:

```csharp
class EmailAddressAttribute : RegularExpressionAttribute
{
    EmailAddressAttribute()
        : base(@"^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$") { }    

    override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;
        return base.IsValid(value);
    }
}    

class SwedishPostalCodeAttribute : RegularExpressionAttribute
{
    SwedishPostalCodeAttribute(bool optionalSpace = false)
        : base(optionalSpace ? "^\\d{3}\\ ?\\d{2}$" : "^\\d{5}$") { }    

    override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;
        return base.IsValid(value);
    }
}    

class SwedishSsnAttribute : RegularExpressionAttribute
{
    SwedishSsnAttribute()
        : base("^\\d{6}-?\\d{4}$") { }    

    override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;    

        //Remove possible dash
        var noDash = value.ToString().Replace("-", "");    

        //Verify the Luhn algorithm
        var sum = 0;
        for (var i = 0; i &lt; 9; i++)
        {
            var tmpInt = int.Parse(noDash[i].ToString());
            tmpInt = tmpInt * (((i + 1) % 2) + 1);
            sum += (tmpInt &gt; 9) ? tmpInt - 9 : tmpInt;
            sum = (sum &gt; 10) ? sum - 10 : sum;
        }    

        //Verify the check digit
        return (10 - sum) == int.Parse(noDash[9].ToString());
    }
}    

class UrlAttribute : RegularExpressionAttribute
{
    UrlAttribute()
        : base(@"^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;amp;:/~\+#]*[\w\-\@?^=%&amp;amp;/~\+#])?") { }    

    override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;
        return base.IsValid(value);
    }
}
```

As you can see, the e-mail, postal code and url attributes only use a regular expression as well as null/empty conditions, while the social security number attribute does a bit more.

With this approach, `RegularExpressionAttribute` can be validated client-side and a second validation can then take place server-side, when the user posts the form.

This makes it possible to gather all validation in one place. If you need to use the regex as a separate value, it can be accessed with the `Pattern` property of the validation attribute.