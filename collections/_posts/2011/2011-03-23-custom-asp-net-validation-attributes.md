---
title: Custom ASP.NET validation attributes
date:  2011-03-23 12:00:00 +0100
tags:  archive
icon:  dotnet
---

ASP.NET validation attributes is a great way of making it easy to validate C#
properties in different ways, client and server side. Let's look at how we can
create our own validation attributes.

I use custom validation attributes all the time, for instance to validate:

- E-mail addresses
- Postal codes
- Social security numbers
- URLs

These examples could to some extent be validated with regular expressions, but
some may require deeper validation than just looking at string format.

With custom validation attributes, we can both perform basic regex validation
client-side and go deeper server-side whenever needed. 

Let's look at some examples:

```csharp
public class EmailAddressAttribute : RegularExpressionAttribute
{
    public EmailAddressAttribute()
        : base(@"^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$") { }    

    public override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;
        return base.IsValid(value);
    }
}    

public class SwedishPostalCodeAttribute : RegularExpressionAttribute
{
    public SwedishPostalCodeAttribute(bool optionalSpace = false)
        : base(optionalSpace ? "^\\d{3}\\ ?\\d{2}$" : "^\\d{5}$") { }    

    public override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;
        return base.IsValid(value);
    }
}    

public class SwedishSsnAttribute : RegularExpressionAttribute
{
    public SwedishSsnAttribute()
        : base("^\\d{6}-?\\d{4}$") { }    

    public override bool IsValid(object value)
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

public class UrlAttribute : RegularExpressionAttribute
{
    public UrlAttribute()
        : base(@"^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;amp;:/~\+#]*[\w\-\@?^=%&amp;amp;/~\+#])?") { }    

    public override bool IsValid(object value)
    {
        if (value == null || value.ToString().IsNullOrEmpty())
            return false;
        return base.IsValid(value);
    }
}
```

As you can see, the e-mail, postal code and url attributes only use a regular
expression as well as a null/empty condition, while the social security number
attribute does a bit more.

What is great with this approach, is that `RegularExpressionAttribute` can be
validated client-side. A second validation will then take place server-side,
when the user posts a form with an ssn.

This makes it possible to gather regex and further validation in one place. If
you need to use the regex separately, it can be accessed with the `Pattern` property
of the validation attribute. 

