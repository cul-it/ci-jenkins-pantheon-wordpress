# ACF Image Aspect Ratio Crop Field

[![Build Status](https://img.shields.io/github/workflow/status/joppuyo/acf-image-aspect-ratio-crop/PHP%20Composer?logo=github)](https://github.com/joppuyo/acf-image-aspect-ratio-crop/actions)
[![Test status](https://img.shields.io/github/workflow/status/joppuyo/acf-image-aspect-ratio-crop/Test?label=tests&logo=github)](https://github.com/joppuyo/acf-image-aspect-ratio-crop/actions)
[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/acf-image-aspect-ratio-crop?logo=wordpress)](https://wordpress.org/plugins/acf-image-aspect-ratio-crop/)
[![Active installs](https://img.shields.io/wordpress/plugin/installs/acf-image-aspect-ratio-crop.svg?logo=wordpress)](https://wordpress.org/plugins/acf-image-aspect-ratio-crop/advanced/)

A field for Advanced Custom Fields that forces the user to crop their image to specific aspect ratio or pixel size after uploading. Using an aspect ratio is especially useful in responsive image use cases.

After cropping, a new cropped image variant is created in the gallery and saved into the post. Thumbnails are also generated for the new image. User can re-crop the original image at any time from the post page.

The cropped image variants are hidden by default in the media browser and on the media page but you can view them by selecting the "list view" on the media page.

## Modes of operation

There are two modes of operation: aspect ratio and pixel size. You can select this option when creating the field in ACF field options.

### Aspect ratio

Use this option if you want the image to be of specific aspect ratio like 16:9 but the pixel size is not important.

After selecting an image, user can select an area from the image that matches this aspect ratio. When crop button is pressed, the area is cropped from the original image.

If you need a smaller image size, you make use of WordPress's thumbnail functionality to access a smaller version of the image.

### Pixel size

Use this option if you need a specific pixel size image like 640x480. User will not be able to select an image smaller than the defined pixel size.

After selecting an image, user can select an area from the image they want, which can be larger than the pixel size but may not be smaller. The aspect ratio of the selection is locked according to the pixel size.

When crop button is pressed, the area is cropped from the original image. After the crop is complete, the image will be automatically scaled down to the pixel size. This means the final image will always be the specified size.

## Cropping an image to 16:9 aspect ratio

![Screenshot of cropping an image](assets/images/screenshot-1.jpg?v=1552838494)

## Cropping in progress

![Screenshot of cropping in progress](assets/images/screenshot-2.jpg?v=1552838494)


## Option to re-crop the image after upload

![Screenshot of the image field](assets/images/screenshot-3.png?v=1552838494)

## Download

You can download the plugin from the [WordPress plugin directory](https://wordpress.org/plugins/acf-image-aspect-ratio-crop/), or download the latest release as a zip file from [GitHub releases](https://github.com/joppuyo/acf-image-aspect-ratio-crop/releases).

## Frequently Asked Questions

###  Can I use this plugin with a front-end acf_form?

Unfortunately this is not supported right now since the plugin requires `upload_files` capability to access the media library. If user does not have this permission, a basic upload dialog will be displayed without a cropper. You can enable cropping by assigning  `upload_files`  capability to the user role but this means that users are able to access the media library like admin users. I will look into implementing front-end form cropping without needing this capability in a future release of this plugin.

### Can I access metadata in the original image from a cropped image? 

Yes, the original image data is saved under `original_image` key in the returned ACF array. You can access data such as alt text, description and title this way.

### How is this different from the other plugin?

This plugin is similar to [Advanced Custom Fields: Image Crop Add-on](https://wordpress.org/plugins/acf-image-crop-add-on/). I originally created a fork of that plugin to add functionality I need: specifying an aspect ratio instead of pixel size. Unfortunately the plugin doesn't seem to be maintained anymore so my pull request was not merged.

So I created **ACF Image Aspect Ratio Crop** from scratch as an alternative to **ACF Image Crop**.

Possibility to use a pixel size instead of aspect ratio was added later on because I got so many requests for adding that feature.

The other plugin is not actively maintained and does not work well with latest ACF versions. I try to maintain this plugin as best as I can when new versions of ACF and WordPress come out.

## Thanks

Special thanks to Anders Thorborg for [ACF Image Crop](https://github.com/andersthorborg/ACF-Image-Crop) which served as a inspiration for this plugin. Also, thanks to Fengyuan Chen for the [cropper.js](https://fengyuanchen.github.io/cropperjs/) library!

## License

GPL v2 or later
