# Getting-To-Philosophy
Developed an API where users can give any WIKI URL and know the number of hops to reach the philosophy page and the path taken to reach the philopsophy page

Clicking on the first link in the main text of a Wikipedia article, and then repeating the process for subsequent articles, will usually lead to the Philosophy article. As of February 2016, 97% of all articles in Wikipedia eventually lead to the article Philosophy. One major loop was found in the past: the knowledge article lead to facts, facts to experience, experience back to knowledge. However, as of July 7, 2017, the facts article has been changed to link to verifiability, verifiability to verificationism, and verificationism to philosophy.

Following the chain consists of:

Clicking on the first non-parenthesized, non-italicized link
Ignoring external links, links to the current page, or red links (links to non-existent pages)
Stopping when reaching "Philosophy", a page with no links or a page that does not exist, or when a loop occurs

NOTE: We used the mysql database to store the number of hops and the path taken. So to see this properly working change the Username, Password and database name in the Store_DB function inside the ParseURL.php file. Also I am including the phil.SQL file where you can use it to import the table structure to your mysql database.
 After that use RequestData.html to give the input as WIKI URL and you can see the result(No. of hops, Path taken to reach the Philosophy page).

Technologies Used: PHP, MySQL, HTML, CSS, Javascript, AJAX, jQuery.
