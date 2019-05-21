# Styling the Republish Button

If you'd like to alter the appearance of the default `Republish` button, you'll need to do so through your [child] theme's CSS.

![default republish button](img/default-republish-button.png)

Here are the default stylings of the `Republish` button:

```
.side-widget.creative_commons_sharing button.creative-commons-button, .widget.creative_commons_sharing button.creative-commons-button {
    width: 100%;
    background-color: #5499db;
    border: 1px solid #2863a7;
    color: #fff;
    padding: 1em;
    font-size: 1.25em;
    margin: 0 0 1em 0;
    display: block;
    border-radius: .25em;
    font-weight: bold;
    text-shadow: 1px 1px 1px #2863a7;
}
```

Go ahead and copy those styles into your theme's CSS file and replace any attributes that you'd like to modify.