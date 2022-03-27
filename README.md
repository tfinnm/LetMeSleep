# LetMeSleep

No one likes getting up for an 8am class, so **LetMeSleep** helps you by generating a William &amp; Mary class schedule that avoids early classes, in turn, letting you sleep.

<img src="https://raw.githubusercontent.com/tfinnm/LetMeSleep/main/bridge.png" alt="W&M Bridge" width="100%"/>

## Examples

<img src="https://raw.githubusercontent.com/tfinnm/LetMeSleep/main/documentation/Screenshot.PNG" alt="Screenshot of UI" width="100%"/>

### Sample Outputs

 - [John Doe](https://raw.githubusercontent.com/tfinnm/LetMeSleep/main/out/John%20Doe_202220_623f996f336ae.pdf)

 - [John Doe Jr](https://raw.githubusercontent.com/tfinnm/LetMeSleep/main/out/John%20J.%20Doe%20Jr._202220_623f9930c6af7.pdf)

 - [John Doe III](https://raw.githubusercontent.com/tfinnm/LetMeSleep/main/out/John%20Doe%20III_202220_623f99602e412.pdf)

 - [View All Sample Outputs](https://github.com/tfinnm/LetMeSleep/tree/main/out)

## Features

> *Sleep? What's that??* - College Students, Probably.

### What Does LetMeSleep Check For?

 - [x] Courses Must Be For The Selected Term (Such As Spring 2022)
 - [x] Courses Must Be For A Selected Subject
 - [x] All Selected Subjects Have A Course Scheduled (If None Are Available, **LetMeSleep** Will Warn You)
 - [x] Courses Must Be Open & Have Space Available
 - [x] Courses Must Be For The Correct Level (ie. Graduate or Undergraduate)
 - [x] If Requested By User, Courses Must Not Have Any Pre-Reqs (Enabled/Disabled On The Form)
 - [x] Courses Cannot Overlap With Other Courses That Have Been Scheduled
 
 ### What Information Will I Get Back?

 - [x] A Suggested Course Schedule Broken Down By Day
 - [x] Information About Each Suggested Course Including:
	- [x] Course Description
	- [X] # of Credit Hours
	- [x] Time & Place Of The Course
	- [x] Instructor(s) Of The Course
	- [x] Course Pre-Reqs (If Applicable)
	- [x] Course Code
 
 ### What Options Do I Have?
 
 - [x] **Student Name:** This *Required Text* Field Is Used To Help Identify Schedules Once They Are Downloaded Or Printed
 - [x] **Term:** This *Required Single-Selection* Field Allows You To Select Which Term To Generate A Schedule For
 - [x] **Subjects:** This *Required Multi-Select* Field Allows You To Select Which Subjects To Take Courses In
 - [x] **Level:** This *Required Single-Selection* Field Allows You To Indicate Whether You Are A Graduate or Undergraduate Student
 - [x] **Start Time:** This *Require Single-Selection* Field Allows You To Pick The ***Earliest*** Time You Are Willing To Go To Class
 - [x] **Avoid Pre-Reqs:** This *Check-box* Allows You To Indicate Whether Or Not *LetMeSleep* Should Avoid Picking Courses With Pre-Reqs
 
 ### How Will I Get The Output From LetMeSleep?

**LetMeSleep** Will Open A PDF Of It's Output In Your Browser That You Can Download Or Print. For Examples Of The Output, [Click Here](https://github.com/tfinnm/LetMeSleep/#sample-outputs)

 ### How Does **LetMeSleep** Work?
 
 **LetMeSleep** is powered by a PHP backend that generates a PDF using FPDF based on data retrieved from William & Mary IT's Open Data API using cURL. The Frontend Is Built In Bootstrap 3 (Javascript/CSS/HTML). **LetMeSleep** is also designed to match the William & Mary Branding Guidelines.
 
 ### Should I Use ***LetMeSleep**?
 
 For Actually Making Your Schedule, No, You Probably Shouldn't. **LetMeSleep** Absolutely Works, It Will Generate A Schedule That You Could Actually Turn Around And Register For, However, You Will Most Likely Get A Lot More Out Of College By Registering For Courses Based On What Interests You, Not Based On Some Computer Althorithm. That Said, If You Just Want To Play Around With It Or See What It Thinks You Should've Taken, By All Means, Go For It. If For Some Reason You Actually Do Use **LetMeSleep** To Decide Your Course Schedule, Let Me Know And I'll Give You A Shoutout Right Here.