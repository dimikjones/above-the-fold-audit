# Above The Fold Audit

Shows which hyperlinks were seen above the fold when someone opens the homepage over the past 7 days.

## The problem to be solved

The plugin empowers website owners to make data-driven decisions about their homepage's layout and content, ensuring that the most important elements are seen immediately by their audience, ultimately aiming to improve user experience, engagement, and data accessibility.

## Technical specification of the design

-   **Identifying Visible Hyperlinks and Screen Size (JavaScript):** This directly tackles the social challenge of understanding what content (specifically interactive links) is immediately visible to a visitor when they land on the homepage. It also captures the visitor's viewport dimensions, which is crucial for optimizing responsive designs and ensuring critical content is indeed "above the fold" for different devices. This solves the problem of lack of visibility into initial user perception and environmental context.

-   **Injecting the Script on the Homepage only:** By automating the data collection process for every homepage visit, the plugin solves the problem of manual or incomplete data collection regarding above-the-fold content visibility.

-   **Endpoint for Data Reception and Database Storage (PHP Backend):** This provides a robust and scalable infrastructure for securely receiving and persisting the collected analytics data. It solves the problem of efficient and reliable data handling and storage for granular user interaction analytics.

-   **Admin Page for Data Display (PHP Backend with Template):** Offering a user-friendly interface within the WordPress dashboard allows website owners to easily review and interpret the collected data without needing external tools or technical expertise. This solves the problem of difficulty in accessing and interpreting raw analytics data.

-   **Periodically Removing Outdated Data:** This specific functionality ensures that the database remains lean and performant by automatically deleting data older than 7 days. This directly solves the problem of database bloat and the associated performance degradation that can occur with continuous data collection.

## The technical decisions I made and why

Since I am now on vacation, my child isn't going to kindergarten, and we are renovating our apartment, I have made the following decisions :)

-   I have used a custom plugin boilerplate to be on my own ground in order to save some time, which also uses modern OOP and PSR.

-   I presumed that this is some premium or custom-made plugin for someone where functionality should be isolated without overriding/modifications, so I decided to use the `final` keyword for PHP classes. I am mentioning this because some developers find the use of the `final` keyword excessive, and some developers advocate never to use it at all. In this case, it makes a lot of sense.

-   The JavaScript file for the admin is loaded globally for the admin backend, which could be enhanced by loading it only for specific admin pages. Also, the script can be initialized from the browser console with `aboveFoldAudit.aboveFoldAuditHomePageAnalysis.init();` in case the admin wants to run and collect data by manually testing different screen sizes before collecting real user data. Additionally, there is a commented-out piece of code for reinitializing the script on resize, which can also enhance testing by the admin, but that is up for discussion since it wasn't a requirement. An additional tweak would be to ignore WordPress admin bar links since a visitor could also be logged in with the admin bar enabled.

-   Per the proposed solution, I have created a `Table` class for handling data, and the `RestApi` class as the central hub for all interactions between the plugin's frontend analytics script and backend data management. An `AboveTheFoldPage` class is responsible for registering the admin page which displays collected data through a template.

-   The plugin passes PHPCS inspection https://drive.google.com/file/d/1d9Fp2JIStcdKBIgvikayHnq-2QCRXqF3/view?usp=drive_link

## How this solution achieves the admin’s desired outcome per the user story

-   The plugin creates a complete system that automates the collection of crucial user interaction data, securely stores it, presents it in an easily digestible format for the administrator, and intelligently manages the data lifecycle to keep the system efficient.

## Why this direction is a better solution

-   My approach for the plugin uses modern OOP with PSR. Although a functional approach might be a better option for such small functionality, in regards to future scaling and extending, the OOP approach allows better file organization and implementation of new modules.

-   While we all have some starter templates, frameworks, or boilerplates with a set of standards that we are following, all integration programming decisions can be discussed upfront and afterward, which is highly desirable.

-   From my perspective, for future scaling, although I didn't have enough time, I would create a system for admin page registration and REST API endpoints in order to be able to easily add admin parent and subpages as needed and to add new routes as needed.