---
title:  "Get the week number of a date in C#"
date:   2009-05-25 19:46:00 +0100
tags: 	.net c#
---


**NOTE:** This post was written in 2009. Although the core logic has not changed
since then, the implementation has. For the lastest implementation, check out my
[NExtra](https://github.com/danielsaidi/nextra) project on GitHub.


When working with the DateTime class, it is not that straightforward to calculate
the week number for a certain date. This post will present an alternative to the
standard Globalization approach.

Note that this approach only works if the first weekday is Monday, the first week
of a year is the one that includes the first Thursday of that year, and the last
week of a year is the one that precedes the first calendar week of the next year.

I found the original version of the function at *Simen Sandelien's* website. The
web site has disappeared since then, and since I have modified the code a lot, I
chose to publish it.


```csharp
/// <summary>Get the week number of a certain date, provided that
/// the first day of the week is Monday, the first week of a year
/// is the one that includes the first Thursday of that year and
/// the last week of a year is the one that immediately precedes
/// the first calendar week of the next year.
/// </summary>
/// <param name="date">Date of interest.</param>
/// <returns>The week number.</returns>
public static int GetWeekNumber(this DateTime date)
{
    //Constants
    const int JAN = 1;
    const int DEC = 12;
    const int LASTDAYOFDEC = 31;
    const int FIRSTDAYOFJAN = 1;
    const int THURSDAY = 4;
    bool thursdayFlag = false;

    //Get the day number since the beginning of the year
    int dayOfYear = date.DayOfYear;

    //Get the first and last weekday of the year
    int startWeekDay = (int)(new DateTime(date.Year, JAN, FIRSTDAYOFJAN)).DayOfWeek;
    int endWeekDay = (int)(new DateTime(date.Year, DEC, LASTDAYOFDEC)).DayOfWeek;

    //Compensate for using monday as the first day of the week
    if (startWeekDay == 0) {
        startWeekDay = 7;
    }
    if (endWeekDay == 0) {
        endWeekDay = 7;
    }

    //Calculate the number of days in the first week
    int daysInFirstWeek = 8 - (startWeekDay);

    //Year starting and ending on a thursday will have 53 weeks
    if (startWeekDay == THURSDAY || endWeekDay == THURSDAY) {
        thursdayFlag = true;
    }

    //We begin by calculating the number of FULL weeks between
    //the year start and our date. The number is rounded up so
    //the smallest possible value is 0.
    int fullWeeks = (int)Math.Ceiling((dayOfYear - (daysInFirstWeek)) / 7.0);
    int result = fullWeeks;

    //If the first week of the year has at least four days, the
    //actual week number for our date can be incremented by one.
    if (daysInFirstWeek >= THURSDAY) {
        result = result + 1;
    }

    //If the week number is larger than 52 (and the year doesn't
    //start or end on a thursday), the correct week number is 1.
    if (result > 52 && !thursdayFlag) {
        result = 1;
    }

    //If the week number is still 0, it means that we are trying
    //to evaluate the week number for a week that belongs to the
    //previous year (since it has 3 days or less in this year).
    //We therefore execute this function recursively, using the
    //last day of the previous year.
    if (result == 0) {
        result = GetWeekNumber(new DateTime(date.Year - 1, DEC, LASTDAYOFDEC));
    }

    return result;
}
```


With this function available, it is a piece of cake to get the first and last date
for a certain date's week, as such:


```csharp
/// <summary>
/// Get the first date of the week for a certain date, provided
/// that the first day of the week is Monday, the first week of
/// a year is the one that includes the first Thursday of that
/// year and the last week of a year is the one that immediately
/// precedes the first calendar week of the next year.
/// </summary>
/// <param name="date">ISO 8601 date of interest.</param>
/// <returns>The first week date.</returns>
public static DateTime GetFirstDateOfWeek(this DateTime date)
{
    if (date == DateTime.MinValue) {
        return date;
    }

    int week = date.GetWeekNumber();
    
    while (week == date.GetWeekNumber()) {
        date = date.AddDays(-1);
    }

    return date.AddDays(1);
}
```


and


```csharp
/// <summary>
/// Get the last date of the week for a certain date, provided
/// that the first day of the week is Monday, the first week of
/// a year is the one that includes the first Thursday of that
/// year and the last week of a year is the one that immediately
/// precedes the first calendar week of the next year.
/// </summary>
/// <param name="date">ISO 8601 date of interest.</param>
/// <returns>The first week date.</returns>
public static DateTime GetLastDateOfWeek(this DateTime date)
{
    if (date == DateTime.MaxValue) {
        return date;
    }

    int week = date.GetWeekNumber();

    while (week == date.GetWeekNumber()) {
        date = date.AddDays(1);
    }

    return date.AddDays(-1);
}
```


I hope that this helps anyone having problem with retrieving the week number. If
you want the latest version, checkout [NExtra](https://github.com/danielsaidi/nextra).



