# Configuring Plugin Settings

To configure the settings for the Creative Commons Sharing plugin, navigate to the `Settings` -> `Reading` inside of your WordPress admin panel once the plugin is installed
and activated.

![navigating to plugin settings](img/settings.png)

## Creative Commons Sharing Policy

The `Creative Commons Sharing Policy` field is where you will be able to input your rules and policies for users to see before they copy and paste your content to republish. A good example of a CCS policy can be found [here](https://www.propublica.org/steal-our-stories/).

![creative commons sharing policy field](img/creative-commons-sharing-policy.png)

## Creative Commons Sharing Google Analytics ID

In order to track your republished post pageviews in Google Analytics, you will need to insert your Google Analytics tracking ID into the field labeled `Creative Commons Sharing Google Analytics ID`.

![creative commons sharing google analytics id field](img/creative-commons-sharing-analytics-id.png)

To find your Google Analytics ID, first log into your Google Analytics account. Once logged in, click the `Admin` button on the bottom left corner.

![google analytics admin button](img/google-analytics-admin-button.png)

Once inside the admin panel, click into `Property Settings` for the property you want the tracking ID for.

![google analytics property settings](img/google-analytics-property-settings.png)

Inside of the `Property Settings` pane is where you will see your tracking ID.

![google analytics tracking id](img/google-analytics-tracking-id.png)

## Creative Commons Tracking Code

Once you have your Google Analytics ID set, you'll be able to view a sample tracking code that you will be able to copy and paste in order to manually insert into specific types of articles that you'd like to track. Just remember to swap out `YOUR-POST-ID` with the actual ID of the post you're going to insert the pixel into.

![creative commons sharing manual tracking code](img/creative-commons-sharing-tracking-code.png)

To find the ID of your post that you'd like to use, navigate to the edit page for the specific post. Once on the editor page, you can grab your post ID from the `post` parameter in the url.

![how to grab post id](img/find-post-id.png)