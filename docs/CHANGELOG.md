Changelog
=========

1.3.6 (May, 05, 2020)
-------------------
- Fix #116: Completed lists pagination broken
- Fix #117: Task list settings updated_at field not updated on task save
- Chg #97: Reduced Completed list pagination size from 20 to 10
- Enh: Updated translations

1.3.5 (April, 06, 2020)
--------------------
- Chg: Added 1.5 defer compatibility

1.3.4 (March, 31, 2020)
--------------------
- Fix #109: Invalid time saved when using non system timezone
- Chg: Raised HumHub min version to 1.3.11
- Fix: Task permissions displayed on container without task module installed (https://github.com/humhub/humhub/issues/3828)
- Enh: Improved event handler exception handling

1.3.3 (October, 16, 2019)
--------------------
- Fix: Invalid date range in calendar integration
- Fix: Invalid date format used when default input format is fixed
- Enh: Translation updates

1.3.2 (October, 16, 2019)
--------------------
- Enh: 1.4 csp nonce support

1.3.1 (October, 04, 2019)
--------------------
- Enh: Allow module installation on user profile
- Fix: Task list sorting not working when dragging to last index
- Chng: The export now has an Container, ContainerType and ContainerId field instead of Space and SpaceId
- Fix #12: Do not notify assigned user when user assigns himself
- Fix #96: Created by filter not working
- Fix: Task space navigation only visible for space members

1.3.0 (May, 9, 2019)
--------------------
- Enh: Filter by container related tasks in "Your tasks" snippet.
- Enh: Global overview
- Enh: Added move content feature
- Enh: Added Tabbed Menu to single task view
- Enh: Added `searchPaginationSize` module configuration 
- Enh: Added `showTopMenuItem` and `topMenuSort` module configuration
- Fix: RemindEnd notification displays start date instead of end date
- Enh: Added date filter to search view
- Enh: Enhanced list view usability
- Enh: Use of new richtext editor
- Enh: Added uid field for calendar integration

1.2.2 (October 15, 2018)
--------------------
- Fix: Reminder mail link broken
- Fix: Broken mobile view

1.2.1 (September 19, 2018)
--------------------
- Fix: Finished Task List contains unfinished list

1.2.0 (September 19, 2018)
--------------------
- Fix: Reminder queue logic not compatible with v1.3
- Chng: Changed min HumHub Version to v1.3

1.1.15 (September 19, 2018)
--------------------
- Fix: TaskAssigned notification not sent when creating a task

1.1.14 (August 22, 2018)
--------------------
- Fix: TaskList not dropped for deinstalled container and module deinstallation

1.1.13 (August 22, 2018)
--------------------
- Fix Use of save drop function in uninstall.php

1.1.12 (August 22, 2018)
--------------------
- Fix #76 Error when deleting task list for MyISAM based db

1.1.11 (July 18, 2018)
--------------------
- Fix #72 File attachment on task creation not working

1.1.10 (July 13, 2018)
--------------------
- Fix #71 german translation error

1.1.9 (July 2, 2018)
--------------------
- Fix: PHP 7.2 compatibility issues


1.1.8 (June 13, 2018)
----------------------
- Fix #70 Task with no responsible user can be edited by each member
- Chng: Disallow default Managetask permission for Usergrup Members
- Fix # 69 removed invalid message source


1.1.7 (May 29, 2018)
-----------------------
- Fix # 69 removed invalid message source


1.1.6 (May 25, 2018)
-----------------------
- Fix reminder not sent
- Fix assignment notifications are sent for already existing assignments


1.1.4 (May 24, 2018)
-----------------------
- Fix removed invalid full day span check


1.1.3 (May 24, 2018)
-----------------------
- Fix user list tooltip on task list items

1.1.2 (May 24, 2018)
-----------------------
- Fix user list tooltip on task list items

1.1.1 (May 24, 2018)
-----------------------
- Fix notification and activity module id

1.1.0 (May 23, 2018))
-----------------------
- Enh: Added sortable Task lists
- Enh: Added Task review (staxDB)
- Enh: Added sortable Task checklist (staxDB)
- Enh: Added Task responsible user (staxDB)
- Enh: Extended Permission System 
- Enh: Added Search view
- Enh: Added calendar export (staxDB)
- Enh: Added extended scheduling (staxDB)
- Chg: Major refactoring + redesign
