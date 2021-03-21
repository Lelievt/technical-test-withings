# technical-test-withings
technical test for withings in PHP

# What I'd improve
I'd check if the requests did perform correctly by checking the response code and data.
I'd perform more checks over the responses format before trying to access it.
Also check that the weight I get and display is indeed the latest.
Try to store the access token and refresh token somewhere to not do the Oauth process at each refresh.
Implement the refresh token behaviour once the access token is expired.
I'd organize the routes differently to not have all the code in the index.php and have a cleaner code.
For example, /callback to get the authorization token, /token to get the access token, /getLatestWeight to request the last weight registered and so on.