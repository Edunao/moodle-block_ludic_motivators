# moodle-block_ludic_motivators
Moodle block plugin developed by Edunao for the ludic_motivators research project


# Motivator Specs

## Avatar
This widget is effectively the graphical version of the GOALS widget described below.
It displays an svg image with a set of calcs that can be hidden or shown depending on the goals that have been achieved.
The php should pass down to the javascript a list of goals as:
* layer name for the svg image
* State (0=not achieved, 1=previously achieved, 2=just achieved)
The widget should display:
* a first div showing the image with all of the layers for previously achieved goals unmasked
* a second div that appears when there are goals that have just been achieved, that displays only the layers for the goals that have been newly achieved


## Badges
This widget should display badges representing user achievements.
The php should pass down to the javascript:
* a list of course goals as:
  * badge name
  * icon identifier
  * State (0=not achieved, 1=previously achieved, 2=just achieved)
* a list of global goals as:
  * layer name for the svg image
  * State (0=not achieved, 1=previously achieved, 2=just achieved)
The widget should display:
* a first div displaying icons for the course golas, sleecting between the 'achieved' and 'not achieved' icons appropriately
* a second div that appears when there are course goals that have just been achieved, that displays the newly achieved goals
* a third div that displays an svg image representing the global goals with layers that can be displayed to represent the goals that have been achieved


## Goals
This widget displays a list of 'goals' as text strings with a checkmark next to the ones that have been achieved
The php should pass down to the javascript a list of entries as:
* Displayable name
* State (0=not achieved, 1=previously achieved, 2=just achieved)
The widget should display:
* a first div listing all goals with checkboxes next to those that have been achieved
* a second div that appears when there are goals that have just been achieved, that lists these goals


## Progress
This widget displays user progress as defined as the { number_of_questions_answered_successfully / total_number_of_questsions_in_the_coure }
Progress is quantised down to scale of 0..8
The widget has 2 views: A view for display in courses and a second view for display in the user's /my/ page
    The view within a course:
        shows a tree branch as an svg with 8 optional layers. The progress value (0..8) determines which of the layers will be hidden and which revealed
    The view for the /my/ page:
        shows an image of trees with 8 optional layers per course (making 14 x 8 = 112 optional layers in all).
        the principle is the same as the view within a course, except that all courses are being represented on the same image


## Ranking
The ranking widget shows the relative achievement level of an individual compared to the rest of the participants in the same course.
The php should pass down to the javascript:
* The user's score (or null if no exercises yet completed)
* The class average and best scores
* The user's rank as a position within the class (eg 1st, 2nd, ...)
The display should ideally take the form of a vertical bar:
* y axis represents { number of correct answers in finished exercises / number of questsions in finished exercises }
* a marker should indicate the class averag
* a second marker should indicate the class best
* a third marker should indicate the user's own level
For initial implementation it would be OK to display a bar graph with 3 bars for class-average, class-best and self


## Score
The score represents the total of the points earned so far in the exercise plus any bonuses earned
For reasons of future flexibility the php should pass down to the javascript:
* The previous total score
* The new total score
* A list of bonuses as:
  * bonus name
  * bonus value
  * bonus state (0=not achieved, 1=previously achieved, 2=just achieved)

For the initial implementation the javascript should display:
* A first div containing:
  * The latest total score
* A scond div, that appears only if the score progresses, containing:
  * The number of new points received from the last answer
  * Any new bonuses achieved with last answer


## Timer
The timer represents the time past since the start of the exercise
The php should pass down to the javascript the timestamp of the start of exercise
The javascript should display the difference between the current time and the start time, auto-updated once per second
The motivator should only be displayed if the current exercise has already been attempted once previously.
The previous attempt times should be listed beneath the timer.
### NOTE:
The graphic design suggests drawing a histogram of previous attempt times but for an initial implementation it will be ok to simply show the best time so far as a single line of text beneath the timer


# Configuration
Configuration currently via JSON file only
