Requirement

Create a simple subscription platform (RESTful APIs with MySQL, Vue.js UI) in which users can subscribe to one or more websites
Whenever a new post is published on a particular website, all its subscribers shall receive an email with the post title and description in it. No authentication of any kind is required.
Requirements:
MUST:
● Endpoint to create a "post" for a "particular website".
● UI and Endpoint to allow user to subscribe to a "particular website" with validations.
● Use commands to send emails to the subscribers.
● Use queues to schedule sending in the background.
● Follow TDD when implementing all the features.
● Write migrations for the required tables.
● No duplicate posts should get sent to subscribers by email.
NICE TO HAVE:
● Seeded data of the websites.
● Open API documentation (or) Postman collection demonstrating available APIs & their usage.
● Use of the latest Laravel version.
● Use of contracts & services.
● Use of caching wherever applicable.
● Use of events/listeners.
OUTPUTS:
● Deploy the code on a public GitHub repository and provide the link
● Provide special instructions (if any) to make to codebase run on our local/remote platform.
