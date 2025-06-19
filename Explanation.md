# Above The Fold Audit

Shows which hyperlinks were seen above the fold when someone opens homepage over the past 7 days.

## The problem to be solved

The plugin empowers website owners to make data-driven decisions about their homepage's layout and content, ensuring that the most important elements are seen immediately by their audience, ultimately aiming to improve user experience, engagement and data accessibility.

## Technical specification of the design

- Identifying Visible Hyperlinks and Screen Size (JavaScript): This directly tackles the challenge of understanding what content (specifically interactive links) is immediately visible to a visitor when they land on the homepage. It also captures the visitor's viewport dimensions, which is crucial for optimizing responsive designs and ensuring critical content is indeed "above the fold" for different devices. This solves the problem of lack of visibility into initial user perception and environmental context.

- Injecting the Script on the Homepage only: By automating the data collection process for every homepage visit, the plugin solves the problem of manual or incomplete data collection regarding above-the-fold content visibility.

- Endpoint for Data Reception and Database Storage (PHP Backend): This provides a robust and scalable infrastructure for securely receiving and persisting the collected analytics data. It solves the problem of efficient and reliable data handling and storage for granular user interaction analytics.

- Admin Page for Data Display (PHP Backend with Template): Offering a user-friendly interface within the WordPress dashboard allows website owners to easily review and interpret the collected data without needing external tools or technical expertise. This solves the problem of difficulty in accessing and interpreting raw analytics data.

- Periodically Removing Outdated Data: This specific functionality, ensures that the database remains lean and performant by automatically deleting data older than 7 days. This directly solves the problem of database bloat and the associated performance degradation that can occur with continuous data collection.

## The technical decisions I made and why

Since I am now on vacation, child isn't going to kindergarten, and we are renovating apartment I have made following decisions :)

- I have used custom plugin boilerplate to be on my own ground in order to save some time which also use modern OOP and PSR.

- I presumed that this is some premium or custom made plugin for someone where functionality should be isolated without overriding/modifications so I decided to use use final keyword for PHP classes. I am mentioning this because some developers find the use of the final keyword excessive, and some developers advocate never to use it at all. In this case it makes a lot of sense.

- JavaScript file for admin is loaded globally for admin back end which could be enhanced by loading only for specific admin page only. Laso script can be initialized from browser console with aboveFoldAudit.aboveFoldAuditHomePageAnalysis.init(); in case that admin want to run and collect data by manually testing different screen sizes before collecting real user data. Also there is commented out piece of code for reinitializing script on resize which also can enhance testing by admin but that is up for a discussion since it wasn't a requirement.

- Per proposed solution I have created Table class for handling data, RestApi classes the central hub for all interactions between your plugins frontend analytics script and backend data management. AboveTheFoldPage class is responsible for registering admin page which display collected data trough template.

## How this solution achieves the admin’s desired outcome per the user story

- Plugin creates a complete system that automates the collection of crucial user interaction data, securely stores it, presents it in an easily digestible format for the administrator, and intelligently manages the data lifecycle to keep the system efficient.

## Why this direction is a better solution

- My approach for the plugin uses modern OOP with PSR, although functional approach might be a better option for such a small functionality in regards to future scaling and extending OOP approach allows better file organisation and implementation of new modules.

- While we all have some starter templates, frameworks or boilerplates with set of standards that we are following all integration programming decisions can be discussed upfront and after which is highly desirable.

- From my point, for future scaling, although I didn't have enough time, I would create a system for admin pages registration and REST API endpoints in order to be able to easily add admin parent and subpages as needed and to add new routes as needed.